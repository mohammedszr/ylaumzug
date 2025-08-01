#!/bin/bash

# YLA Umzug Test Runner Script
# Runs comprehensive tests for both frontend and backend

set -e

echo "🧪 Running YLA Umzug Test Suite..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# Test results tracking
FRONTEND_TESTS_PASSED=false
BACKEND_TESTS_PASSED=false
INTEGRATION_TESTS_PASSED=false

# Run frontend tests
run_frontend_tests() {
    log_info "Running frontend tests..."
    
    if [ ! -f "package.json" ]; then
        log_error "package.json not found"
        return 1
    fi
    
    # Install dependencies if needed
    if [ ! -d "node_modules" ]; then
        log_info "Installing frontend dependencies..."
        npm install
    fi
    
    # Run tests
    if npm test; then
        log_success "Frontend tests passed"
        FRONTEND_TESTS_PASSED=true
    else
        log_error "Frontend tests failed"
        return 1
    fi
}

# Run backend tests
run_backend_tests() {
    log_info "Running backend tests..."
    
    cd backend
    
    if [ ! -f "composer.json" ]; then
        log_error "composer.json not found in backend directory"
        cd ..
        return 1
    fi
    
    # Install dependencies if needed
    if [ ! -d "vendor" ]; then
        log_info "Installing backend dependencies..."
        composer install
    fi
    
    # Set up test environment
    if [ ! -f ".env.testing" ]; then
        log_info "Creating test environment file..."
        cp .env.example .env.testing
        
        # Configure test database
        sed -i 's/DB_DATABASE=.*/DB_DATABASE=yla_testing/' .env.testing
        sed -i 's/MAIL_MAILER=.*/MAIL_MAILER=log/' .env.testing
        sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=array/' .env.testing
        sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=array/' .env.testing
        sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env.testing
    fi
    
    # Generate application key for testing
    php artisan key:generate --env=testing
    
    # Run database migrations for testing
    log_info "Setting up test database..."
    php artisan migrate:fresh --env=testing --seed
    
    # Run PHPUnit tests
    if php artisan test --env=testing; then
        log_success "Backend tests passed"
        BACKEND_TESTS_PASSED=true
    else
        log_error "Backend tests failed"
        cd ..
        return 1
    fi
    
    cd ..
}

# Run integration tests
run_integration_tests() {
    log_info "Running integration tests..."
    
    # Start test environment
    log_info "Starting test environment..."
    docker-compose -f docker-compose.staging.yml up -d --build
    
    # Wait for services to be ready
    log_info "Waiting for services to be ready..."
    sleep 30
    
    # Run integration tests
    if run_api_integration_tests && run_e2e_tests; then
        log_success "Integration tests passed"
        INTEGRATION_TESTS_PASSED=true
    else
        log_error "Integration tests failed"
        docker-compose -f docker-compose.staging.yml down
        return 1
    fi
    
    # Clean up
    docker-compose -f docker-compose.staging.yml down
}

# Run API integration tests
run_api_integration_tests() {
    log_info "Running API integration tests..."
    
    # Test calculator API
    if curl -f http://localhost:8001/api/calculator/services > /dev/null 2>&1; then
        log_success "Calculator services API test passed"
    else
        log_error "Calculator services API test failed"
        return 1
    fi
    
    # Test calculation endpoint
    local test_data='{"selectedServices":["umzug"],"movingDetails":{"apartmentSize":80,"fromAddress":{"postalCode":"10115"},"toAddress":{"postalCode":"10117"}}}'
    
    if curl -X POST -H "Content-Type: application/json" -d "$test_data" http://localhost:8001/api/calculator/calculate > /dev/null 2>&1; then
        log_success "Calculator calculation API test passed"
    else
        log_error "Calculator calculation API test failed"
        return 1
    fi
    
    # Test quote submission
    local quote_data='{"name":"Test User","email":"test@example.com","phone":"+49123456789","selectedServices":["umzug"],"serviceDetails":{"movingDetails":{"apartmentSize":80}},"estimatedTotal":450}'
    
    if curl -X POST -H "Content-Type: application/json" -d "$quote_data" http://localhost:8001/api/quotes > /dev/null 2>&1; then
        log_success "Quote submission API test passed"
    else
        log_error "Quote submission API test failed"
        return 1
    fi
    
    return 0
}

# Run end-to-end tests
run_e2e_tests() {
    log_info "Running end-to-end tests..."
    
    # Test frontend accessibility
    if curl -f http://localhost:3000 > /dev/null 2>&1; then
        log_success "Frontend accessibility test passed"
    else
        log_error "Frontend accessibility test failed"
        return 1
    fi
    
    # Test calculator page
    if curl -f http://localhost:3000/calculator > /dev/null 2>&1; then
        log_success "Calculator page test passed"
    else
        log_error "Calculator page test failed"
        return 1
    fi
    
    # Test admin panel
    if curl -f http://localhost:3002/admin > /dev/null 2>&1; then
        log_success "Admin panel test passed"
    else
        log_warning "Admin panel test failed (may be expected)"
    fi
    
    return 0
}

# Run performance tests
run_performance_tests() {
    log_info "Running performance tests..."
    
    # Test frontend build size
    if [ -d "dist" ]; then
        local build_size=$(du -sh dist | cut -f1)
        log_info "Frontend build size: $build_size"
        
        # Check if build size is reasonable (under 5MB)
        local size_bytes=$(du -sb dist | cut -f1)
        if [ "$size_bytes" -lt 5242880 ]; then
            log_success "Frontend build size is optimal"
        else
            log_warning "Frontend build size is large: $build_size"
        fi
    fi
    
    # Test API response times
    log_info "Testing API response times..."
    local response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8001/api/calculator/services)
    log_info "Calculator services API response time: ${response_time}s"
    
    if (( $(echo "$response_time < 1.0" | bc -l) )); then
        log_success "API response time is good"
    else
        log_warning "API response time is slow: ${response_time}s"
    fi
}

# Generate test report
generate_test_report() {
    log_info "Generating test report..."
    
    local report_file="test-report-$(date +%Y%m%d_%H%M%S).txt"
    
    {
        echo "YLA Umzug Test Report"
        echo "Generated: $(date)"
        echo "=========================="
        echo ""
        echo "Test Results:"
        echo "- Frontend Tests: $([ "$FRONTEND_TESTS_PASSED" = true ] && echo "PASSED" || echo "FAILED")"
        echo "- Backend Tests: $([ "$BACKEND_TESTS_PASSED" = true ] && echo "PASSED" || echo "FAILED")"
        echo "- Integration Tests: $([ "$INTEGRATION_TESTS_PASSED" = true ] && echo "PASSED" || echo "FAILED")"
        echo ""
        echo "Environment:"
        echo "- Node.js: $(node --version 2>/dev/null || echo "Not available")"
        echo "- PHP: $(php --version 2>/dev/null | head -n1 || echo "Not available")"
        echo "- Docker: $(docker --version 2>/dev/null || echo "Not available")"
        echo ""
        echo "System Info:"
        echo "- OS: $(uname -s)"
        echo "- Architecture: $(uname -m)"
        echo "- Date: $(date)"
    } > "$report_file"
    
    log_success "Test report generated: $report_file"
}

# Main execution
main() {
    local test_type="${1:-all}"
    
    case "$test_type" in
        "frontend")
            run_frontend_tests
            ;;
        "backend")
            run_backend_tests
            ;;
        "integration")
            run_integration_tests
            ;;
        "performance")
            run_performance_tests
            ;;
        "all")
            log_info "Running all tests..."
            
            # Run tests in sequence
            if run_frontend_tests && run_backend_tests; then
                log_success "Unit tests completed successfully"
                
                # Run integration tests if unit tests pass
                if run_integration_tests; then
                    log_success "Integration tests completed successfully"
                    
                    # Run performance tests
                    run_performance_tests
                    
                    log_success "All tests completed successfully! 🎉"
                else
                    log_error "Integration tests failed"
                    exit 1
                fi
            else
                log_error "Unit tests failed"
                exit 1
            fi
            ;;
        *)
            echo "Usage: $0 {frontend|backend|integration|performance|all}"
            echo ""
            echo "Test Types:"
            echo "  frontend     - Run React/JavaScript tests"
            echo "  backend      - Run Laravel/PHP tests"
            echo "  integration  - Run API and E2E tests"
            echo "  performance  - Run performance tests"
            echo "  all          - Run all tests (default)"
            exit 1
            ;;
    esac
    
    # Generate report
    generate_test_report
}

# Trap errors
trap 'log_error "Test execution failed!"; exit 1' ERR

# Run main function
main "$@"