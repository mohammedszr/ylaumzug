# Azure Git Deployment Guide - Umzugs und Hausleistungen Rechner YLA Umzug

## 🎯 Project Renamed Successfully

The project has been renamed to **"Umzugs und Hausleistungen Rechner YLA Umzug"** across all configuration files:

- ✅ `package.json` - Frontend package name updated
- ✅ `backend/composer.json` - Backend package name and description updated  
- ✅ `backend/config/app.php` - Application name updated
- ✅ `index.html` - Page title updated
- ✅ `backend/README.md` - Documentation updated
- ✅ `DEVELOPER_DOCUMENTATION.md` - Project overview updated

## 🔧 Finishing Touches Needed

### 1. Environment Configuration
```bash
# Update .env files with production values
cp backend/.env.example backend/.env.production
```

**Required Environment Variables:**
```env
# Backend (.env.production)
APP_NAME="Umzugs und Hausleistungen Rechner YLA Umzug"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (Azure MySQL/PostgreSQL)
DB_CONNECTION=mysql
DB_HOST=your-azure-db-host.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=yla_umzug_prod
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

# Mail Configuration (Azure Communication Services or SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=info@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@your-domain.com
MAIL_FROM_NAME="YLA Umzug"

# Cache & Session (Azure Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-cache.redis.cache.windows.net
REDIS_PASSWORD=your-redis-key
REDIS_PORT=6380
REDIS_CLIENT=predis

# File Storage (Azure Blob Storage)
FILESYSTEM_DISK=azure
AZURE_STORAGE_ACCOUNT=yourstorageaccount
AZURE_STORAGE_KEY=your-storage-key
AZURE_STORAGE_CONTAINER=uploads
```

### 2. Security Hardening
```bash
# Generate new application key
cd backend
php artisan key:generate --show

# Update CORS settings in backend/config/cors.php
# Set allowed origins to your domain only
```

### 3. Performance Optimization
```bash
# Frontend build optimization
npm run build

# Backend optimization
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. SSL Certificate Setup
- Configure SSL certificate in Azure App Service
- Update all HTTP references to HTTPS
- Set up automatic HTTP to HTTPS redirect

### 5. Database Migration Preparation
```bash
# Create production migration script
cd backend
php artisan migrate:status
php artisan db:seed --class=ProductionSeeder
```

## 🚀 Azure Git Deployment Setup

### Step 1: Create Azure Resources

**1.1 Create Resource Group**
```bash
az group create --name rg-yla-umzug --location "West Europe"
```

**1.2 Create App Service Plan**
```bash
az appservice plan create \
  --name plan-yla-umzug \
  --resource-group rg-yla-umzug \
  --sku B2 \
  --is-linux
```

**1.3 Create Web Apps**
```bash
# Frontend App
az webapp create \
  --resource-group rg-yla-umzug \
  --plan plan-yla-umzug \
  --name yla-umzug-frontend \
  --runtime "NODE|18-lts"

# Backend App  
az webapp create \
  --resource-group rg-yla-umzug \
  --plan plan-yla-umzug \
  --name yla-umzug-backend \
  --runtime "PHP|8.2"
```

**1.4 Create Database**
```bash
# MySQL Flexible Server
az mysql flexible-server create \
  --resource-group rg-yla-umzug \
  --name yla-umzug-db \
  --admin-user dbadmin \
  --admin-password "YourSecurePassword123!" \
  --sku-name Standard_B1ms \
  --tier Burstable \
  --public-access 0.0.0.0 \
  --storage-size 20 \
  --version 8.0
```

**1.5 Create Redis Cache**
```bash
az redis create \
  --resource-group rg-yla-umzug \
  --name yla-umzug-cache \
  --location "West Europe" \
  --sku Basic \
  --vm-size c0
```

### Step 2: Configure Git Deployment

**2.1 Initialize Git Repository (if not already done)**
```bash
git init
git add .
git commit -m "Initial commit: Umzugs und Hausleistungen Rechner YLA Umzug"
```

**2.2 Create Azure DevOps Repository**
```bash
# Create new repository in Azure DevOps
# https://dev.azure.com/your-organization/_git/yla-umzug-calculator

# Add Azure DevOps as remote
git remote add origin https://dev.azure.com/your-organization/_git/yla-umzug-calculator
git branch -M main
git push -u origin main
```

**2.3 Configure Deployment Credentials**
```bash
# Set deployment credentials for both apps
az webapp deployment user set \
  --user-name yla-deployment \
  --password "YourDeploymentPassword123!"
```

**2.4 Configure Git Deployment**
```bash
# Frontend deployment
az webapp deployment source config \
  --resource-group rg-yla-umzug \
  --name yla-umzug-frontend \
  --repo-url https://dev.azure.com/your-organization/_git/yla-umzug-calculator \
  --branch main \
  --manual-integration

# Backend deployment  
az webapp deployment source config \
  --resource-group rg-yla-umzug \
  --name yla-umzug-backend \
  --repo-url https://dev.azure.com/your-organization/_git/yla-umzug-calculator \
  --branch main \
  --manual-integration
```

### Step 3: Configure Build and Deployment

**3.1 Create Azure Pipeline (azure-pipelines.yml)**
```yaml
trigger:
- main

pool:
  vmImage: 'ubuntu-latest'

variables:
  buildConfiguration: 'Release'

stages:
- stage: Build
  displayName: 'Build Stage'
  jobs:
  - job: BuildFrontend
    displayName: 'Build Frontend'
    steps:
    - task: NodeTool@0
      inputs:
        versionSpec: '18.x'
      displayName: 'Install Node.js'
    
    - script: |
        npm ci
        npm run build
      displayName: 'npm install and build'
    
    - task: PublishBuildArtifacts@1
      inputs:
        PathtoPublish: 'dist'
        ArtifactName: 'frontend'
        publishLocation: 'Container'

  - job: BuildBackend
    displayName: 'Build Backend'
    steps:
    - task: UsePhpVersion@0
      inputs:
        versionSpec: '8.2'
      displayName: 'Use PHP 8.2'
    
    - script: |
        cd backend
        composer install --optimize-autoloader --no-dev
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
      displayName: 'Composer install and optimize'
    
    - task: PublishBuildArtifacts@1
      inputs:
        PathtoPublish: 'backend'
        ArtifactName: 'backend'
        publishLocation: 'Container'

- stage: Deploy
  displayName: 'Deploy Stage'
  dependsOn: Build
  jobs:
  - deployment: DeployFrontend
    displayName: 'Deploy Frontend'
    environment: 'production'
    strategy:
      runOnce:
        deploy:
          steps:
          - task: AzureWebApp@1
            inputs:
              azureSubscription: 'your-service-connection'
              appType: 'webAppLinux'
              appName: 'yla-umzug-frontend'
              package: '$(Pipeline.Workspace)/frontend'

  - deployment: DeployBackend
    displayName: 'Deploy Backend'
    environment: 'production'
    strategy:
      runOnce:
        deploy:
          steps:
          - task: AzureWebApp@1
            inputs:
              azureSubscription: 'your-service-connection'
              appType: 'webAppLinux'
              appName: 'yla-umzug-backend'
              package: '$(Pipeline.Workspace)/backend'
```

**3.2 Configure App Settings**
```bash
# Frontend app settings
az webapp config appsettings set \
  --resource-group rg-yla-umzug \
  --name yla-umzug-frontend \
  --settings \
    WEBSITE_NODE_DEFAULT_VERSION="18.17.0" \
    SCM_DO_BUILD_DURING_DEPLOYMENT=true \
    VITE_API_URL="https://yla-umzug-backend.azurewebsites.net"

# Backend app settings
az webapp config appsettings set \
  --resource-group rg-yla-umzug \
  --name yla-umzug-backend \
  --settings \
    APP_NAME="Umzugs und Hausleistungen Rechner YLA Umzug" \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_URL="https://yla-umzug-backend.azurewebsites.net" \
    DB_CONNECTION=mysql \
    DB_HOST="yla-umzug-db.mysql.database.azure.com" \
    DB_PORT=3306 \
    DB_DATABASE=yla_umzug \
    DB_USERNAME=dbadmin \
    DB_PASSWORD="YourSecurePassword123!"
```

### Step 4: Domain and SSL Configuration

**4.1 Configure Custom Domain**
```bash
# Add custom domain
az webapp config hostname add \
  --resource-group rg-yla-umzug \
  --webapp-name yla-umzug-frontend \
  --hostname www.yla-umzug.de

# Enable SSL
az webapp config ssl bind \
  --resource-group rg-yla-umzug \
  --name yla-umzug-frontend \
  --certificate-thumbprint your-cert-thumbprint \
  --ssl-type SNI
```

**4.2 Configure CDN (Optional)**
```bash
az cdn profile create \
  --resource-group rg-yla-umzug \
  --name yla-umzug-cdn \
  --sku Standard_Microsoft

az cdn endpoint create \
  --resource-group rg-yla-umzug \
  --profile-name yla-umzug-cdn \
  --name yla-umzug-assets \
  --origin yla-umzug-frontend.azurewebsites.net
```

## 📋 Pre-Deployment Checklist

### Code Quality
- [ ] All tests passing (`./scripts/test-runner.sh all`)
- [ ] Code linting completed
- [ ] Security vulnerabilities checked
- [ ] Performance optimization completed

### Configuration
- [ ] Production environment variables set
- [ ] Database connection strings configured
- [ ] Email service configured
- [ ] Redis cache configured
- [ ] File storage configured

### Security
- [ ] SSL certificates installed
- [ ] CORS settings configured
- [ ] Authentication tokens updated
- [ ] Sensitive data removed from code
- [ ] Security headers configured

### Performance
- [ ] Frontend assets optimized
- [ ] Backend caching enabled
- [ ] Database indexes created
- [ ] CDN configured (if needed)

### Monitoring
- [ ] Application Insights configured
- [ ] Log Analytics workspace created
- [ ] Health check endpoints configured
- [ ] Alert rules configured

## 🚀 Deployment Commands

### Initial Deployment
```bash
# 1. Push code to Azure DevOps
git add .
git commit -m "feat: ready for production deployment"
git push origin main

# 2. Trigger manual deployment
az webapp deployment source sync \
  --resource-group rg-yla-umzug \
  --name yla-umzug-frontend

az webapp deployment source sync \
  --resource-group rg-yla-umzug \
  --name yla-umzug-backend

# 3. Run database migrations
az webapp ssh --resource-group rg-yla-umzug --name yla-umzug-backend
cd /home/site/wwwroot
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
```

### Continuous Deployment
```bash
# Enable continuous deployment
az webapp deployment source config \
  --resource-group rg-yla-umzug \
  --name yla-umzug-frontend \
  --repo-url https://dev.azure.com/your-organization/_git/yla-umzug-calculator \
  --branch main \
  --git-token your-personal-access-token
```

## 🔍 Post-Deployment Verification

### Health Checks
```bash
# Check frontend
curl -I https://yla-umzug-frontend.azurewebsites.net

# Check backend API
curl -I https://yla-umzug-backend.azurewebsites.net/api/health

# Check database connection
curl https://yla-umzug-backend.azurewebsites.net/api/calculator/services
```

### Performance Testing
```bash
# Load testing with Azure Load Testing
az load test create \
  --resource-group rg-yla-umzug \
  --name yla-umzug-load-test \
  --test-plan load-test-plan.jmx
```

## 📊 Monitoring and Maintenance

### Application Insights
```bash
# Create Application Insights
az monitor app-insights component create \
  --resource-group rg-yla-umzug \
  --app yla-umzug-insights \
  --location "West Europe" \
  --application-type web
```

### Log Monitoring
```bash
# View application logs
az webapp log tail \
  --resource-group rg-yla-umzug \
  --name yla-umzug-backend

# Download logs
az webapp log download \
  --resource-group rg-yla-umzug \
  --name yla-umzug-backend
```

### Backup Strategy
```bash
# Database backup
az mysql flexible-server backup create \
  --resource-group rg-yla-umzug \
  --server-name yla-umzug-db \
  --backup-name daily-backup-$(date +%Y%m%d)

# App backup
az webapp config backup create \
  --resource-group rg-yla-umzug \
  --webapp-name yla-umzug-backend \
  --backup-name app-backup-$(date +%Y%m%d) \
  --storage-account-url "your-storage-url"
```

## 🆘 Troubleshooting

### Common Issues

**Deployment Fails**
```bash
# Check deployment logs
az webapp log deployment list --resource-group rg-yla-umzug --name yla-umzug-backend
az webapp log deployment show --resource-group rg-yla-umzug --name yla-umzug-backend --deployment-id <id>
```

**Database Connection Issues**
```bash
# Test database connectivity
az mysql flexible-server connect \
  --name yla-umzug-db \
  --admin-user dbadmin \
  --admin-password "YourSecurePassword123!"
```

**Performance Issues**
```bash
# Scale up app service plan
az appservice plan update \
  --resource-group rg-yla-umzug \
  --name plan-yla-umzug \
  --sku P1V2

# Enable auto-scaling
az monitor autoscale create \
  --resource-group rg-yla-umzug \
  --resource yla-umzug-frontend \
  --resource-type Microsoft.Web/sites \
  --name yla-umzug-autoscale \
  --min-count 1 \
  --max-count 3 \
  --count 1
```

## 📞 Support Resources

- **Azure Documentation**: https://docs.microsoft.com/azure/
- **Laravel Deployment**: https://laravel.com/docs/deployment
- **React Deployment**: https://create-react-app.dev/docs/deployment/
- **Azure DevOps**: https://docs.microsoft.com/azure/devops/

---

**Project**: Umzugs und Hausleistungen Rechner YLA Umzug  
**Last Updated**: $(date)  
**Version**: 1.0.0