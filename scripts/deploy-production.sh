#!/bin/bash

# YLA Umzug Production Deployment Script
# This script deploys the application to production environment

set -e  # Exit on any error

echo "🚀 Starting YLA Umzug Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PRODUCTION_ENV="production"
DOCKER_COMPOSE_FILE="docker-compose.yml"
BACKUP_DIR="./backups/production/$(date +%Y%m%d_%H%M%S)"
DOMAIN="yla-umzug.de"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed"
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose is not installed"
        exit 1
    fi
    
    if [ ! -f "$DOCKER_COMPOSE_FILE" ]; then
        log_error "Docker Compose file not found: $DOCKER_COMPOSE_FILE"
        exit 1
    fi
    
    if [ ! -f "./backend/.env" ]; then
        log_error "Production .env file not found"
        exit 1
    fi
    
    log_success "Prerequisites check passed"
}

# Create backup
create_backup() {
    log_info "Creating production backup..."
    
    mkdir -p "$BACKUP_DIR"
    
    # Backup database
    if docker-compose ps mysql | grep -q "Up"; then
        log_info "Backing up production database..."
        docker-compose exec -T mysql mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" yla_production > "$BACKUP_DIR/database.sql"
        log_success "Database backup created"
    else
        log_warning "Database container not running, skipping database backup"
    fi
    
    # Backup storage files
    if [ -d "./backend/storage" ]; then
        log_info "Backing up storage files..."
        cp -r ./backend/storage "$BACKUP_DIR/"
        log_success "Storage backup created"
    fi
    
    # Backup SSL certificates
    if [ -d "./nginx/ssl" ]; then
        log_info "Backing up SSL certificates..."
        cp -r ./nginx/ssl "$BACKUP_DIR/"
        log_success "SSL certificates backed up"
    fi
    
    log_success "Production backup created in $BACKUP_DIR"
}

# Deploy to production
deploy() {
    log_info "Starting production deployment..."
    
    # Confirmation prompt
    read -p "⚠️  Are you sure you want to deploy to PRODUCTION? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        log_info "Deployment cancelled"
        exit 0
    fi
    
    # Pull latest changes
    if [ -d ".git" ]; then
        log_info "Pulling latest changes from main branch..."
        git checkout main
        git pull origin main
    fi
    
    # Build production images
    log_info "Building production Docker images..."
    docker-compose build --no-cache
    
    # Stop existing containers gracefully
    log_info "Stopping existing containers..."
    docker-compose down --timeout 30
    
    # Start services
    log_info "Starting production services..."
    docker-compose up -d
    
    # Wait for services to be ready
    log_info "Waiting for services to be ready..."
    sleep 60
    
    # Run Laravel setup
    setup_laravel_production
    
    # Run production tests
    run_production_tests
    
    # Update SSL certificates if needed
    update_ssl_certificates
    
    log_success "Production deployment completed successfully!"
}

# Setup Laravel for production
setup_laravel_production() {
    log_info "Setting up Laravel for production..."
    
    # Wait for database to be ready
    log_info "Waiting for database to be ready..."
    sleep 20
    
    # Run migrations
    log_info "Running database migrations..."
    docker-compose exec backend php artisan migrate --force
    
    # Optimize Laravel for production
    log_info "Optimizing Laravel for production..."
    docker-compose exec backend php artisan config:cache
    docker-compose exec backend php artisan route:cache
    docker-compose exec backend php artisan view:cache
    docker-compose exec backend php artisan event:cache
    
    # Clear any development caches
    docker-compose exec backend php artisan cache:clear
    
    # Set proper permissions
    log_info "Setting production permissions..."
    docker-compose exec backend chown -R www-data:www-data /var/www/html/storage
    docker-compose exec backend chmod -R 755 /var/www/html/storage
    
    log_success "Laravel production setup completed"
}

# Run production tests
run_production_tests() {
    log_info "Running production tests..."
    
    # Smoke tests
    log_info "Running smoke tests..."
    
    # Test frontend
    if curl -f https://$DOMAIN/health > /dev/null 2>&1; then
        log_success "Frontend smoke test passed"
    else
        log_error "Frontend smoke test failed"
        return 1
    fi
    
    # Test backend API
    if curl -f https://$DOMAIN/api/health > /dev/null 2>&1; then
        log_success "Backend API smoke test passed"
    else
        log_error "Backend API smoke test failed"
        return 1
    fi
    
    # Test calculator endpoint
    if curl -f https://$DOMAIN/api/calculator/services > /dev/null 2>&1; then
        log_success "Calculator API smoke test passed"
    else
        log_error "Calculator API smoke test failed"
        return 1
    fi
    
    log_success "Production tests completed"
}

# Update SSL certificates
update_ssl_certificates() {
    log_info "Checking SSL certificates..."
    
    # Check if certificates exist and are valid
    if [ -f "./nginx/ssl/fullchain.pem" ] && [ -f "./nginx/ssl/privkey.pem" ]; then
        # Check certificate expiration (30 days warning)
        if openssl x509 -checkend 2592000 -noout -in ./nginx/ssl/fullchain.pem; then
            log_success "SSL certificates are valid"
        else
            log_warning "SSL certificates expire within 30 days"
            # Here you would typically renew certificates with Let's Encrypt
            # certbot renew --nginx
        fi
    else
        log_warning "SSL certificates not found"
    fi
}

# Health check for production
health_check() {
    log_info "Performing production health check..."
    
    # Check all services
    services=("frontend" "backend" "mysql" "redis" "payload" "nginx")
    
    for service in "${services[@]}"; do
        if docker-compose ps $service | grep -q "Up"; then
            log_success "$service is running"
        else
            log_error "$service is not running"
            return 1
        fi
    done
    
    # Check external connectivity
    if curl -f https://$DOMAIN > /dev/null 2>&1; then
        log_success "Website is accessible externally"
    else
        log_error "Website is not accessible externally"
        return 1
    fi
    
    # Check SSL certificate
    if curl -f https://$DOMAIN > /dev/null 2>&1; then
        log_success "SSL certificate is working"
    else
        log_error "SSL certificate issue"
        return 1
    fi
    
    log_success "Production health check completed"
}

# Monitor deployment
monitor() {
    log_info "Monitoring production deployment..."
    
    # Monitor for 5 minutes
    for i in {1..30}; do
        if curl -f https://$DOMAIN/health > /dev/null 2>&1; then
            echo -n "✓"
        else
            echo -n "✗"
        fi
        sleep 10
    done
    
    echo ""
    log_success "Monitoring completed"
}

# Rollback production
rollback() {
    log_warning "Rolling back production deployment..."
    
    read -p "⚠️  Are you sure you want to rollback PRODUCTION? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        log_info "Rollback cancelled"
        exit 0
    fi
    
    # Stop current containers
    docker-compose down
    
    # Find latest backup
    LATEST_BACKUP=$(ls -t ./backups/production/ | head -n1)
    if [ -z "$LATEST_BACKUP" ]; then
        log_error "No backup found for rollback"
        exit 1
    fi
    
    BACKUP_PATH="./backups/production/$LATEST_BACKUP"
    log_info "Rolling back to backup: $LATEST_BACKUP"
    
    # Restore database
    if [ -f "$BACKUP_PATH/database.sql" ]; then
        log_info "Restoring database..."
        docker-compose up -d mysql
        sleep 20
        docker-compose exec -T mysql mysql -u root -p"$MYSQL_ROOT_PASSWORD" yla_production < "$BACKUP_PATH/database.sql"
    fi
    
    # Restore storage
    if [ -d "$BACKUP_PATH/storage" ]; then
        log_info "Restoring storage..."
        rm -rf ./backend/storage
        cp -r "$BACKUP_PATH/storage" ./backend/
    fi
    
    # Restart services
    docker-compose up -d
    
    log_success "Production rollback completed"
}

# Show production status
show_status() {
    log_info "Production Status:"
    echo ""
    docker-compose ps
    echo ""
    log_info "Production URLs:"
    echo "  Website: https://$DOMAIN"
    echo "  API: https://$DOMAIN/api"
    echo "  Admin: https://$DOMAIN/admin"
    echo ""
    log_info "System Resources:"
    docker stats --no-stream
}

# Main execution
main() {
    case "${1:-deploy}" in
        "deploy")
            check_prerequisites
            create_backup
            deploy
            health_check
            monitor
            show_status
            ;;
        "rollback")
            rollback
            ;;
        "status")
            show_status
            ;;
        "health")
            health_check
            ;;
        "backup")
            create_backup
            ;;
        "monitor")
            monitor
            ;;
        "logs")
            docker-compose logs -f "${2:-}"
            ;;
        "stop")
            read -p "⚠️  Are you sure you want to stop PRODUCTION? (yes/no): " confirm
            if [ "$confirm" == "yes" ]; then
                log_info "Stopping production environment..."
                docker-compose down
                log_success "Production environment stopped"
            else
                log_info "Stop cancelled"
            fi
            ;;
        *)
            echo "Usage: $0 {deploy|rollback|status|health|backup|monitor|logs|stop}"
            echo ""
            echo "Commands:"
            echo "  deploy   - Deploy to production environment (default)"
            echo "  rollback - Rollback to previous backup"
            echo "  status   - Show production status"
            echo "  health   - Run health checks"
            echo "  backup   - Create backup only"
            echo "  monitor  - Monitor deployment"
            echo "  logs     - Show container logs"
            echo "  stop     - Stop production environment"
            exit 1
            ;;
    esac
}

# Trap errors and provide rollback option
trap 'log_error "Production deployment failed! Run: $0 rollback"; exit 1' ERR

# Run main function
main "$@"