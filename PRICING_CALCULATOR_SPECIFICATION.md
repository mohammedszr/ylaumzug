# YLA Umzug - Pricing Calculator Specification

## Purpose & Business Logic

The pricing calculator provides instant cost estimates for three main services:
1. **Umzug (Moving Services)**
2. **Entrümpelung (Decluttering/Clearance)**  
3. **Putzservice (Cleaning Services)**

The calculator serves as a lead generation tool - customers get estimates and then provide contact details for official quotes.

## Data Collection & Variables

### 1. Moving Services (Umzug)

#### Required Input Data:
- **From Address**: Street, postal code, city, floor, elevator availability
- **To Address**: Street, postal code, city, floor, elevator availability  
- **Apartment Size**: Square meters (m²)
- **Room Count**: Number of rooms
- **Transport Volume**: 
  - Number of boxes (estimated)
  - Furniture inventory (beds, wardrobes, sofas, tables, appliances)
- **Special Requirements**: Dismantling needed, fragile items
- **Additional Services**: Assembly, packing, parking permits, storage, disposal
- **Parking Situation**: Available, restricted, difficult

#### Pricing Variables Needed:
```
BASE_PRICE_PER_SQM = €8/m²           // Base rate per square meter
DISTANCE_RATE = €2/km                // Cost per kilometer distance
BOX_HANDLING_RATE = €3/box           // Cost per moving box
FLOOR_SURCHARGE = €50/floor          // Extra cost per floor above 2nd (no elevator)
MINIMUM_MOVING_COST = €300           // Minimum charge for any move

// Additional Services Pricing
FURNITURE_ASSEMBLY = €200            // Dismantling and reassembly
PACKING_SERVICE = €150               // Professional packing
PARKING_PERMIT = €80                 // Arranging no-parking zones
STORAGE_RATE = €100/m²               // Storage per square meter
DISPOSAL_SERVICE = €120              // Old furniture disposal

// Furniture-based pricing (alternative to m²)
FURNITURE_RATES = {
  beds: €50/piece,
  wardrobes: €80/piece,
  sofas: €60/piece,
  tables: €40/piece,
  appliances: €70/piece
}
```

### 2. Decluttering Services (Entrümpelung)

#### Required Input Data:
- **Address**: Street, postal code, city
- **Object Type**: Apartment, house, basement, garage, office, attic
- **Size**: Square meters
- **Volume Estimate**: Low (1-2 containers), Medium (3-5), High (6+), Extreme (hoarding)
- **Waste Types**: Furniture, electronics, hazardous, household items, construction debris
- **Access Details**: Floor, elevator availability, parking situation
- **Clean Handover**: Whether final cleaning is needed
- **Special Notes**: Access difficulties, time constraints

#### Pricing Variables Needed:
```
// Volume-based base pricing
VOLUME_PRICING = {
  low: €300,      // 1-2 containers
  medium: €600,   // 3-5 containers  
  high: €1200,    // 6+ containers
  extreme: €2000  // Hoarding situations
}

// Waste type surcharges
WASTE_SURCHARGES = {
  hazardous: €150,     // Special disposal for chemicals, paint
  electronics: €100,   // E-waste disposal fees
  construction: €200,  // Construction debris disposal
  furniture: €80,      // Large furniture disposal
  household: €0        // Normal household items (included)
}

// Access difficulty surcharges
FLOOR_SURCHARGE_DECLUTTER = €30/floor  // Per floor above ground (no elevator)
ACCESS_DIFFICULTY = €100                // Difficult access locations
CLEAN_HANDOVER = €150                   // Final cleaning service

// Object type multipliers
OBJECT_MULTIPLIERS = {
  apartment: 1.0,
  house: 1.2,
  basement: 0.8,
  garage: 0.9,
  office: 1.1,
  attic: 1.3
}
```

### 3. Cleaning Services (Putzservice)

#### Required Input Data:
- **Object Type**: Apartment, house, office, practice
- **Size**: Square meters
- **Cleaning Areas**: Kitchen, bathroom, living rooms, windows
- **Cleaning Intensity**: Normal, deep cleaning, construction cleaning
- **Frequency**: One-time, weekly, bi-weekly, monthly
- **Access**: Present during cleaning vs. key handover needed

#### Pricing Variables Needed:
```
// Base rates per m² by intensity
CLEANING_RATES = {
  normal: €3/m²,        // Standard cleaning
  deep: €5/m²,          // Deep/thorough cleaning
  construction: €7/m²,   // Post-construction cleaning
  moveout: €6/m²        // Move-out cleaning
}

// Room-specific surcharges
ROOM_SURCHARGES = {
  windows: €2/m²,       // Window cleaning rate
  kitchen: €80,         // Deep kitchen cleaning
  bathroom: €60,        // Deep bathroom cleaning
  balcony: €40,         // Balcony cleaning
  basement: €50         // Basement cleaning
}

// Frequency discounts
FREQUENCY_DISCOUNTS = {
  weekly: 0.20,         // 20% discount
  biweekly: 0.15,       // 15% discount  
  monthly: 0.10,        // 10% discount
  once: 0.00            // No discount
}

MINIMUM_CLEANING_COST = €150  // Minimum charge
```

## Combination Pricing & Discounts

### Multi-Service Discounts:
```
COMBINATION_DISCOUNTS = {
  two_services: 0.10,      // 10% discount for 2 services
  three_services: 0.15,    // 15% discount for 3+ services
}

// Special combination bonuses
MOVING_CLEANING_BONUS = €50     // Extra discount for move + clean
DECLUTTER_CLEANING_BONUS = €75  // Extra discount for declutter + clean
```

### Express Service Surcharge:
```
EXPRESS_SURCHARGE = 0.20        // 20% surcharge for urgent requests
```

### Minimum Order Values:
```
MINIMUM_ORDER_VALUE = €150      // Absolute minimum for any service
```

## Distance Calculation Logic

### German Postal Code Distance Estimation:
```
// Simplified distance calculation based on postal codes
function calculateDistance(fromPostalCode, toPostalCode) {
  if (fromPostalCode === toPostalCode) return 0;
  
  const fromRegion = parseInt(fromPostalCode.substring(0, 1));
  const toRegion = parseInt(toPostalCode.substring(0, 1));
  const difference = Math.abs(parseInt(fromPostalCode) - parseInt(toPostalCode));
  
  // Same region (first digit)
  if (fromRegion === toRegion) {
    if (difference < 50) return 10;
    if (difference < 200) return 25;
    if (difference < 500) return 45;
    return 80;
  }
  
  // Different regions
  const regionDifference = Math.abs(fromRegion - toRegion);
  if (regionDifference === 1) return 120;  // Adjacent regions
  if (regionDifference === 2) return 200;  // 2 regions apart
  if (regionDifference <= 4) return 350;   // 3-4 regions apart
  return 500;  // Far regions
}
```

## Questions for Client Configuration

### 1. Business Information
- **What is your main business email for quote notifications?**
- **What is your business phone number for display?**
- **What is your complete business address?**
- **What postal codes do you serve?** (list all service areas)

### 2. Moving Service Pricing
- **What should be your base rate per square meter?** (currently €8/m²)
- **What should be your distance rate per kilometer?** (currently €2/km)
- **What do you charge for box handling?** (currently €3/box)
- **What is your floor surcharge for stairs?** (currently €50/floor above 2nd)
- **What do you charge for furniture assembly?** (currently €200)
- **What do you charge for packing service?** (currently €150)
- **Do you arrange parking permits? What do you charge?** (currently €80)
- **Do you offer storage? What's your rate per m²?** (currently €100/m²)
- **What do you charge for disposal of old furniture?** (currently €120)

### 3. Decluttering Service Pricing
- **What should be your volume-based pricing?**
  - Low volume (1-2 containers): currently €300
  - Medium volume (3-5 containers): currently €600
  - High volume (6+ containers): currently €1,200
  - Extreme volume (hoarding): currently €2,000
- **What surcharges for special waste types?**
  - Hazardous waste: currently €150
  - Electronics: currently €100
  - Construction debris: currently €200
- **What do you charge for final cleaning?** (currently €150)
- **Floor surcharge for decluttering?** (currently €30/floor)

### 4. Cleaning Service Pricing
- **What are your rates per square meter?**
  - Normal cleaning: currently €3/m²
  - Deep cleaning: currently €5/m²
  - Construction cleaning: currently €7/m²
- **What surcharges for special areas?**
  - Window cleaning: currently €2/m²
  - Deep kitchen cleaning: currently €80
  - Deep bathroom cleaning: currently €60
- **What discounts for regular service?**
  - Weekly: currently 20%
  - Bi-weekly: currently 15%
  - Monthly: currently 10%

### 5. Discounts & Surcharges
- **What discount for multiple services?**
  - 2 services: currently 10%
  - 3+ services: currently 15%
- **What surcharge for express/urgent service?** (currently 20%)
- **What is your minimum order value?** (currently €150)

### 6. Service Areas & Logistics
- **What is your maximum service distance?** (currently 100km)
- **Do you charge extra for certain areas?**
- **What are your standard working hours?**
- **Do you work weekends? Any surcharge?**

## Implementation Priority

### Phase 1: Core Calculator (Week 1)
1. Create simplified pricing calculators for each service
2. Implement basic distance calculation
3. Add combination discounts
4. Create settings management system

### Phase 2: Advanced Features (Week 2)
1. Add all surcharges and special pricing
2. Implement volume/size-based calculations
3. Add express service options
4. Create admin interface for price management

### Phase 3: Optimization (Week 3)
1. Add caching for pricing rules
2. Implement comprehensive validation
3. Add detailed price breakdowns
4. Create analytics and reporting

This specification provides a clear foundation for refactoring the pricing system into manageable, configurable components.