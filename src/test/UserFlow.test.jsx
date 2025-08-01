import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { BrowserRouter } from 'react-router-dom'
import Calculator from '../components/calculator/Calculator'
import * as api from '../lib/api'

// Mock the API
vi.mock('../lib/api', () => ({
  calculatePrice: vi.fn(),
  submitQuote: vi.fn(),
  getServices: vi.fn(),
}))

const MockedCalculator = () => (
  <BrowserRouter>
    <Calculator />
  </BrowserRouter>
)

describe('Complete User Flow Integration Tests', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    
    // Mock successful API responses
    api.getServices.mockResolvedValue({
      success: true,
      services: [
        { key: 'umzug', name: 'Umzug', description: 'Moving service', base_price: 300 },
        { key: 'putzservice', name: 'Putzservice', description: 'Cleaning service', base_price: 150 },
        { key: 'entruempelung', name: 'Entrümpelung', description: 'Decluttering service', base_price: 300 }
      ]
    })

    api.calculatePrice.mockResolvedValue({
      success: true,
      pricing: {
        total: 650.00,
        breakdown: [
          { service: 'Umzug Grundpreis', price: 300.00 },
          { service: 'Entfernungszuschlag', price: 200.00 },
          { service: 'Stockwerk-Zuschlag', price: 150.00 }
        ],
        currency: 'EUR'
      }
    })

    api.submitQuote.mockResolvedValue({
      success: true,
      message: 'Quote submitted successfully',
      quote_number: 'Q-2024-001'
    })
  })

  it('completes full user journey from service selection to quote submission', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Step 1: Service Selection
    await waitFor(() => {
      expect(screen.getByText('Service wählen')).toBeInTheDocument()
    })

    // Select moving service
    await user.click(screen.getByText('Umzug'))
    expect(screen.getByText('Umzug')).toHaveAttribute('aria-pressed', 'true')

    // Proceed to next step
    await user.click(screen.getByText('Weiter'))

    // Step 2: Moving Details
    await waitFor(() => {
      expect(screen.getByText('Umzug Details')).toBeInTheDocument()
    })

    // Fill in apartment size
    const apartmentSizeInput = screen.getByLabelText(/Wohnungsgröße/)
    await user.clear(apartmentSizeInput)
    await user.type(apartmentSizeInput, '80')

    // Fill in addresses
    const fromAddressInput = screen.getByLabelText(/Von Adresse/)
    await user.type(fromAddressInput, 'Musterstraße 1, 10115 Berlin')

    const toAddressInput = screen.getByLabelText(/Nach Adresse/)
    await user.type(toAddressInput, 'Beispielweg 5, 80331 München')

    // Set floor details
    const fromFloorSelect = screen.getByLabelText(/Stockwerk \(Von\)/)
    await user.selectOptions(fromFloorSelect, '3')

    const toFloorSelect = screen.getByLabelText(/Stockwerk \(Nach\)/)
    await user.selectOptions(toFloorSelect, '2')

    // Set elevator availability
    const fromElevatorNo = screen.getByLabelText(/Aufzug vorhanden \(Von\).*Nein/)
    await user.click(fromElevatorNo)

    const toElevatorYes = screen.getByLabelText(/Aufzug vorhanden \(Nach\).*Ja/)
    await user.click(toElevatorYes)

    // Proceed to price calculation
    await user.click(screen.getByText('Weiter'))

    // Step 3: Price Summary
    await waitFor(() => {
      expect(screen.getByText('Kostenübersicht')).toBeInTheDocument()
    })

    // Verify API was called with correct data
    expect(api.calculatePrice).toHaveBeenCalledWith(
      expect.objectContaining({
        selectedServices: ['umzug'],
        movingDetails: expect.objectContaining({
          apartmentSize: 80,
          fromFloor: 3,
          toFloor: 2,
          fromElevator: 'no',
          toElevator: 'yes'
        })
      })
    )

    // Verify price display
    await waitFor(() => {
      expect(screen.getByText('650,00€')).toBeInTheDocument()
      expect(screen.getByText('Umzug Grundpreis')).toBeInTheDocument()
      expect(screen.getByText('Entfernungszuschlag')).toBeInTheDocument()
      expect(screen.getByText('Stockwerk-Zuschlag')).toBeInTheDocument()
    })

    // Proceed to contact form
    await user.click(screen.getByText('Kostenlose Beratung anfordern'))

    // Step 4: Contact Information
    await waitFor(() => {
      expect(screen.getByText('Kontaktdaten')).toBeInTheDocument()
    })

    // Fill contact form
    const nameInput = screen.getByLabelText(/Name/)
    await user.type(nameInput, 'Max Mustermann')

    const emailInput = screen.getByLabelText(/E-Mail/)
    await user.type(emailInput, 'max.mustermann@example.com')

    const phoneInput = screen.getByLabelText(/Telefon/)
    await user.type(phoneInput, '+49 123 456789')

    const messageInput = screen.getByLabelText(/Nachricht/)
    await user.type(messageInput, 'Ich benötige einen Umzug von Berlin nach München.')

    // Accept privacy policy
    const privacyCheckbox = screen.getByLabelText(/Datenschutzerklärung/)
    await user.click(privacyCheckbox)

    // Submit quote
    await user.click(screen.getByText('Angebot anfordern'))

    // Step 5: Verify submission
    await waitFor(() => {
      expect(api.submitQuote).toHaveBeenCalledWith(
        expect.objectContaining({
          name: 'Max Mustermann',
          email: 'max.mustermann@example.com',
          phone: '+49 123 456789',
          message: 'Ich benötige einen Umzug von Berlin nach München.',
          selectedServices: ['umzug'],
          serviceDetails: expect.objectContaining({
            movingDetails: expect.objectContaining({
              apartmentSize: 80,
              fromFloor: 3,
              toFloor: 2
            })
          }),
          estimatedTotal: 650.00,
          pricingBreakdown: expect.arrayContaining([
            expect.objectContaining({ service: 'Umzug Grundpreis', price: 300.00 })
          ])
        })
      )
    })

    // Verify success message
    await waitFor(() => {
      expect(screen.getByText(/Vielen Dank für Ihre Anfrage/)).toBeInTheDocument()
      expect(screen.getByText(/Q-2024-001/)).toBeInTheDocument()
    })
  })

  it('handles multiple services with combination discount flow', async () => {
    const user = userEvent.setup()
    
    // Mock response with combination discount
    api.calculatePrice.mockResolvedValue({
      success: true,
      pricing: {
        total: 850.00,
        breakdown: [
          { service: 'Umzug Grundpreis', price: 300.00 },
          { service: 'Putzservice Grundpreis', price: 150.00 },
          { service: 'Entrümpelung Grundpreis', price: 300.00 },
          { service: 'Entfernungszuschlag', price: 200.00 },
          { service: 'Kombinationsrabatt (3 Services)', price: -100.00 }
        ],
        currency: 'EUR'
      }
    })

    render(<MockedCalculator />)

    // Select multiple services
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })

    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Putzservice'))
    await user.click(screen.getByText('Entrümpelung'))

    await user.click(screen.getByText('Weiter'))

    // Fill moving details
    await waitFor(() => {
      expect(screen.getByText('Umzug Details')).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Wohnungsgröße/), '100')
    await user.type(screen.getByLabelText(/Von Adresse/), 'Berlin')
    await user.type(screen.getByLabelText(/Nach Adresse/), 'München')
    await user.click(screen.getByText('Weiter'))

    // Fill cleaning details
    await waitFor(() => {
      expect(screen.getByText('Putzservice Details')).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Größe/), '100')
    await user.selectOptions(screen.getByLabelText(/Reinigungsart/), 'deep')
    await user.click(screen.getByText('Weiter'))

    // Fill decluttering details
    await waitFor(() => {
      expect(screen.getByText('Entrümpelung Details')).toBeInTheDocument()
    })

    await user.selectOptions(screen.getByLabelText(/Volumen/), 'high')
    await user.click(screen.getByLabelText(/Möbel/))
    await user.click(screen.getByText('Weiter'))

    // Verify combination discount in price summary
    await waitFor(() => {
      expect(screen.getByText('850,00€')).toBeInTheDocument()
      expect(screen.getByText('Kombinationsrabatt (3 Services)')).toBeInTheDocument()
      expect(screen.getByText('-100,00€')).toBeInTheDocument()
    })

    // Complete the flow
    await user.click(screen.getByText('Kostenlose Beratung anfordern'))

    await waitFor(() => {
      expect(screen.getByLabelText(/Name/)).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Name/), 'Anna Schmidt')
    await user.type(screen.getByLabelText(/E-Mail/), 'anna@example.com')
    await user.type(screen.getByLabelText(/Telefon/), '+49 987 654321')
    await user.click(screen.getByLabelText(/Datenschutzerklärung/))
    await user.click(screen.getByText('Angebot anfordern'))

    // Verify all service details are submitted
    await waitFor(() => {
      expect(api.submitQuote).toHaveBeenCalledWith(
        expect.objectContaining({
          selectedServices: ['umzug', 'putzservice', 'entruempelung'],
          serviceDetails: expect.objectContaining({
            movingDetails: expect.any(Object),
            cleaningDetails: expect.any(Object),
            declutterDetails: expect.any(Object)
          }),
          estimatedTotal: 850.00
        })
      )
    })
  })

  it('handles validation errors throughout the flow', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Try to proceed without selecting services
    await waitFor(() => {
      expect(screen.getByText('Weiter')).toBeInTheDocument()
    })

    await user.click(screen.getByText('Weiter'))

    // Should show validation error
    await waitFor(() => {
      expect(screen.getByText('Bitte wählen Sie mindestens einen Service aus.')).toBeInTheDocument()
    })

    // Select service and proceed
    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    // Try to proceed without filling required fields
    await waitFor(() => {
      expect(screen.getByText('Umzug Details')).toBeInTheDocument()
    })

    await user.click(screen.getByText('Weiter'))

    // Should show validation errors for required fields
    await waitFor(() => {
      expect(screen.getByText(/Wohnungsgröße ist erforderlich/)).toBeInTheDocument()
      expect(screen.getByText(/Von Adresse ist erforderlich/)).toBeInTheDocument()
      expect(screen.getByText(/Nach Adresse ist erforderlich/)).toBeInTheDocument()
    })

    // Fill minimum required fields
    await user.type(screen.getByLabelText(/Wohnungsgröße/), '80')
    await user.type(screen.getByLabelText(/Von Adresse/), 'Berlin')
    await user.type(screen.getByLabelText(/Nach Adresse/), 'München')
    await user.click(screen.getByText('Weiter'))

    // Should proceed to price calculation
    await waitFor(() => {
      expect(screen.getByText('Kostenübersicht')).toBeInTheDocument()
    })
  })

  it('handles API errors gracefully during the flow', async () => {
    const user = userEvent.setup()
    
    // Mock API error for calculation
    api.calculatePrice.mockRejectedValue(new Error('Server error'))
    
    render(<MockedCalculator />)

    // Complete service selection and details
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })

    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    await waitFor(() => {
      expect(screen.getByLabelText(/Wohnungsgröße/)).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Wohnungsgröße/), '80')
    await user.type(screen.getByLabelText(/Von Adresse/), 'Berlin')
    await user.type(screen.getByLabelText(/Nach Adresse/), 'München')
    await user.click(screen.getByText('Weiter'))

    // Should show error message
    await waitFor(() => {
      expect(screen.getByText(/Fehler bei der Berechnung/)).toBeInTheDocument()
    })

    // Should provide fallback option
    expect(screen.getByText(/Kontaktieren Sie uns direkt/)).toBeInTheDocument()
  })

  it('preserves user data when navigating back and forth', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Fill service selection
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })

    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    // Fill moving details
    await waitFor(() => {
      expect(screen.getByLabelText(/Wohnungsgröße/)).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Wohnungsgröße/), '80')
    await user.type(screen.getByLabelText(/Von Adresse/), 'Berlin')
    await user.type(screen.getByLabelText(/Nach Adresse/), 'München')

    // Go back to service selection
    await user.click(screen.getByText('Zurück'))

    // Verify service selection is preserved
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toHaveAttribute('aria-pressed', 'true')
    })

    // Go forward again
    await user.click(screen.getByText('Weiter'))

    // Verify form data is preserved
    await waitFor(() => {
      expect(screen.getByLabelText(/Wohnungsgröße/)).toHaveValue(80)
      expect(screen.getByLabelText(/Von Adresse/)).toHaveValue('Berlin')
      expect(screen.getByLabelText(/Nach Adresse/)).toHaveValue('München')
    })
  })
})