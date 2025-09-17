#!/bin/bash

# Smart Merge Script for YLA Umzug
# Merges the better UI from other repo while preserving current backend

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

OTHER_REPO="/Users/abdullahhanifa/Desktop/ylaumzug"
BACKUP_DIR="./backup-before-merge-$(date +%Y%m%d-%H%M%S)"

echo -e "${BLUE}🚀 YLA Umzug Smart Merge Tool${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Verify other repo exists
if [ ! -d "$OTHER_REPO" ]; then
    echo -e "${RED}❌ Error: Other repo not found at $OTHER_REPO${NC}"
    exit 1
fi

# Create backup
mkdir -p "$BACKUP_DIR"
echo -e "${YELLOW}📁 Creating backup at: $BACKUP_DIR${NC}"

# Backup current important files
if [ -f "package.json" ]; then cp package.json "$BACKUP_DIR/"; fi
if [ -f "vite.config.js" ]; then cp vite.config.js "$BACKUP_DIR/"; fi
if [ -d "src" ]; then cp -r src "$BACKUP_DIR/src-backup"; fi

echo -e "${GREEN}✅ Backup created${NC}"
echo ""

# Phase 1: Copy Frontend Configuration
echo -e "${BLUE}📦 Phase 1: Copying Frontend Configuration...${NC}"

# Copy build configuration files
cp "$OTHER_REPO/package.json" ./
echo -e "  ${GREEN}✓${NC} Copied package.json"

cp "$OTHER_REPO/vite.config.js" ./
echo -e "  ${GREEN}✓${NC} Copied vite.config.js"

cp "$OTHER_REPO/tailwind.config.js" ./
echo -e "  ${GREEN}✓${NC} Copied tailwind.config.js"

cp "$OTHER_REPO/postcss.config.js" ./
echo -e "  ${GREEN}✓${NC} Copied postcss.config.js"

# Phase 2: Copy React Frontend
echo ""
echo -e "${BLUE}🎨 Phase 2: Copying React Frontend...${NC}"

# Copy entire src directory
cp -r "$OTHER_REPO/src" ./
echo -e "  ${GREEN}✓${NC} Copied complete React frontend"

# Copy any missing resources
if [ -d "$OTHER_REPO/public" ]; then
    # Only copy non-conflicting files from public
    if [ -f "$OTHER_REPO/public/index.html" ]; then
        cp "$OTHER_REPO/public/index.html" ./public/
        echo -e "  ${GREEN}✓${NC} Copied public/index.html"
    fi
fi

# Phase 3: Install Dependencies
echo ""
echo -e "${BLUE}📦 Phase 3: Installing Dependencies...${NC}"

npm install
echo -e "  ${GREEN}✓${NC} NPM dependencies installed"

# Phase 4: Test Build
echo ""
echo -e "${BLUE}🔧 Phase 4: Testing Build...${NC}"

if npm run build; then
    echo -e "  ${GREEN}✅ Build successful!${NC}"
else
    echo -e "  ${RED}❌ Build failed. Check errors above.${NC}"
    echo -e "  ${YELLOW}💡 You can restore from backup: $BACKUP_DIR${NC}"
    exit 1
fi

# Phase 5: Update Laravel View
echo ""
echo -e "${BLUE}🔧 Phase 5: Updating Laravel Integration...${NC}"

# Check if app.blade.php needs updating
if [ -f "resources/views/app.blade.php" ]; then
    # Backup current view
    cp resources/views/app.blade.php "$BACKUP_DIR/"
    
    # Update to use Vite properly (if needed)
    echo -e "  ${YELLOW}⚠️${NC} Please manually verify resources/views/app.blade.php uses @vite(['src/main.jsx'])"
fi

# Phase 6: Final Verification
echo ""
echo -e "${BLUE}✅ Phase 6: Final Verification...${NC}"

# Check critical files exist
CRITICAL_FILES=(
    "src/main.jsx"
    "src/App.jsx"
    "src/components/calculator/Calculator.jsx"
    "package.json"
    "vite.config.js"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "  ${GREEN}✓${NC} $file exists"
    else
        echo -e "  ${RED}❌${NC} $file missing"
    fi
done

echo ""
echo -e "${GREEN}🎉 Smart Merge Complete!${NC}"
echo ""
echo -e "${YELLOW}📋 What was merged:${NC}"
echo -e "  ✅ Complete React frontend from other repo"
echo -e "  ✅ Build configuration (Vite, Tailwind, PostCSS)"
echo -e "  ✅ NPM dependencies"
echo -e "  ✅ Preserved your Laravel backend"
echo ""
echo -e "${YELLOW}🎯 Next Steps:${NC}"
echo -e "  1. Test the application: ${BLUE}php artisan serve${NC}"
echo -e "  2. Check frontend works: ${BLUE}npm run dev${NC}"
echo -e "  3. Verify calculator functionality"
echo -e "  4. Apply German translations to Filament admin"
echo ""
echo -e "${YELLOW}📁 Backup Location:${NC} $BACKUP_DIR"
echo -e "${YELLOW}💡 If issues occur, restore from backup${NC}"