#!/bin/bash

# YLA Umzug Staging Deployment Script
# This script deploys the application to staging environment

set -e  # Exit on any error

echo "🚀 Starting YLA Umzug Staging Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
STAGING_ENV="staging"
DOCKER_COMPOSE_FILE="docker-compose.staging.yml"
BACKUP_DIR="./backups/$(date +%Y%m%d_%H%M%S)"

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
    
    log_success "Prerequisites check passed"
}

# Create backup
create_backup() {
    log_info "Creating backup..."
    
    mkdir -p "$BACKUP_DIR"
    
    # Backup database
    if docker-compose -f "$DOCKER_COMPOSE_FILE" ps mysql-staging | grep -q "Up"; then
        log_info "Backing up database..."
        docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T mysql-staging mysqldump -u yla_user -pstaging_password yla_staging > "$BACKUP_DIR/database.sql"
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
    
    log_success "Backup created in $BACKUP_DIR"
}

# Build and deploy
deploy() {
    log_info "Starting deployment..."
    
    # Pull latest changes (if in git repository)
    if [ -d ".git" ]; then
        log_info "Pulling latest changes..."
        git pull origin main || log_warning "Git pull failed or not in git repository"
    fi
    
    # Stop existing containers
    log_info "Stopping existing containers..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" down
    
    # Build images
    log_info "Building Docker images..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" build --no-cache
    
    # Start services
    log_info "Starting services..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" up -d
    
    # Wait for services to be ready
    log_info "Waiting for services to be ready..."
    sleep 30
    
    # Run Laravel setup
    setup_laravel
    
    # Run tests
    run_tests
    
    log_success "Deployment completed successfully!"
}

# Setup Laravel
setup_laravel() {
    log_info "Setting up Laravel..."
    
    # Wait for database to be ready
    log_info "Waiting for database to be ready..."
    sleep 10
    
    # Generate application key
    log_info "Generating application key..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan key:generate --force
    
    # Run migrations
    log_info "Running database migrations..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan migrate --force
    
    # Seed database
    log_info "Seeding database..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan db:seed --force
    
    # Clear and cache config
    log_info "Optimizing Laravel..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan config:cache
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan route:cache
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan view:cache
    
    # Set permissions
    log_info "Setting permissions..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging chown -R www-data:www-data /var/www/html/storage
    docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging chmod -R 755 /var/www/html/storage
    
    log_success "Laravel setup completed"
}

# Run tests
run_tests() {
    log_info "Running tests..."
    
    # Backend tests
    log_info "Running backend tests..."
    if docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend-staging php artisan test --env=testing; then
        log_success "Backend tests passed"
    else
        log_error "Backend tests failed"
        return 1
    fi
    
    # Frontend tests (if available)
    log_info "Running frontend tests..."
    if npm test 2>/dev/null; then
        log_success "Frontend tests passed"
    else
        log_warning "Frontend tests not available or failed"
    fi
    
    log_success "Tests completed"
}

# Health check
health_check() {
    log_info "Performing health check..."
    
    # Check frontend
    if curl -f http://localhost:3000/health > /dev/null 2>&1; then
        log_success "Frontend is healthy"
    else
        log_error "Frontend health check failed"
        return 1
    fi
    
    # Check backend
    if curl -f http://localhost:8001/api/health > /dev/null 2>&1; then
        log_success "Backend is healthy"
    else
        log_error "Backend health check failed"
        return 1
    fi
    
    # Check Payload CMS
    if curl -f http://localhost:3002/admin > /dev/null 2>&1; then
        log_success "Payload CMS is healthy"
    else
        log_warning "Payload CMS health check failed"
    fi
    
    log_success "Health check completed"
}

# Rollback function
rollback() {
    log_warning "Rolling back deployment..."
    
    # Stop current containers
    docker-compose -f "$DOCKER_COMPOSE_FILE" down
    
    # Restore database backup if available
    if [ -f "$BACKUP_DIR/database.sql" ]; then
        log_info "Restoring database backup..."
        docker-compose -f "$DOCKER_COMPOSE_FILE" up -d mysql-staging
        sleep 10
        docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T mysql-staging mysql -u yla_user -pstaging_password yla_staging < "$BACKUP_DIR/database.sql"
    fi
    
    # Restore storage backup if available
    if [ -d "$BACKUP_DIR/storage" ]; then
        log_info "Restoring storage backup..."
        rm -rf ./backend/storage
        cp -r "$BACKUP_DIR/storage" ./backend/
    fi
    
    log_success "Rollback completed"
}

# Cleanup old backups (keep last 5)
cleanup_backups() {
    log_info "Cleaning up old backups..."
    
    if [ -d "./backups" ]; then
        cd ./backups
        ls -t | tail -n +6 | xargs -r rm -rf
        cd ..
        log_success "Old backups cleaned up"
    fi
}

# Show deployment status
show_status() {
    log_info "Deployment Status:"
    echo ""
    docker-compose -f "$DOCKER_COMPOSE_FILE" ps
    echo ""
    log_info "Access URLs:"
    echo "  Frontend: http://localhost:3000"
    echo "  Backend API: http://localhost:8001/api"
    echo "  Payload CMS: http://localhost:3002/admin"
    echo "  Nginx Proxy: http://localhost"
}

# Main execution
main() {
    case "${1:-deploy}" in
        "deploy")
            check_prerequisites
            create_backup
            deploy
            health_check
            cleanup_backups
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
        "logs")
            docker-compose -f "$DOCKER_COMPOSE_FILE" logs -f "${2:-}"
            ;;
        "stop")
            log_info "Stopping staging environment..."
            docker-compose -f "$DOCKER_COMPOSE_FILE" down
            log_success "Staging environment stopped"
            ;;
        *)
            echo "Usage: $0 {deploy|rollback|status|health|backup|logs|stop}"
            echo ""
            echo "Commands:"
            echo "  deploy   - Deploy to staging environment (default)"
            echo "  rollback - Rollback to previous backup"
            echo "  status   - Show deployment status"
            echo "  health   - Run health checks"
            echo "  backup   - Create backup only"
            echo "  logs     - Show container logs"
            echo "  stop     - Stop staging environment"
            exit 1
            ;;
    esac
}

# Trap errors and provide rollback option
trap 'log_error "Deployment failed! Run: $0 rollback"; exit 1' ERR

# Run main function
main "$@"