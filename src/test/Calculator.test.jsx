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

describe('Calculator Component', () => {
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
        total: 450.00,
        breakdown: [
          { service: 'Umzug Grundpreis', price: 300.00 },
          { service: 'Entfernungszuschlag', price: 150.00 }
        ],
        currency: 'EUR'
      }
    })

    api.submitQuote.mockResolvedValue({
      success: true,
      message: 'Quote submitted successfully'
    })
  })

  it('renders service selection step initially', async () => {
    render(<MockedCalculator />)
    
    await waitFor(() => {
      expect(screen.getByText('Service wählen')).toBeInTheDocument()
      expect(screen.getByText('Umzug')).toBeInTheDocument()
      expect(screen.getByText('Putzservice')).toBeInTheDocument()
      expect(screen.getByText('Entrümpelung')).toBeInTheDocument()
    })
  })

  it('allows service selection and navigation', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })

    // Select moving service
    const movingButton = screen.getByText('Umzug')
    await user.click(movingButton)

    // Click next to proceed
    const nextButton = screen.getByText('Weiter')
    await user.click(nextButton)

    // Should show moving details form
    await waitFor(() => {
      expect(screen.getByText('Umzug Details')).toBeInTheDocument()
    })
  })

  it('validates required fields before proceeding', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    await waitFor(() => {
      expect(screen.getByText('Weiter')).toBeInTheDocument()
    })

    // Try to proceed without selecting services
    const nextButton = screen.getByText('Weiter')
    await user.click(nextButton)

    // Should show validation message
    await waitFor(() => {
      expect(screen.getByText('Bitte wählen Sie mindestens einen Service aus.')).toBeInTheDocument()
    })
  })

  it('calculates price when moving details are provided', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Select moving service and proceed
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })
    
    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    // Fill in moving details
    await waitFor(() => {
      expect(screen.getByLabelText(/Wohnungsgröße/)).toBeInTheDocument()
    })

    const apartmentSizeInput = screen.getByLabelText(/Wohnungsgröße/)
    await user.type(apartmentSizeInput, '80')

    const fromAddressInput = screen.getByLabelText(/Von Adresse/)
    await user.type(fromAddressInput, 'Berlin, Deutschland')

    const toAddressInput = screen.getByLabelText(/Nach Adresse/)
    await user.type(toAddressInput, 'München, Deutschland')

    // Proceed to price calculation
    await user.click(screen.getByText('Weiter'))

    // Should call calculate API
    await waitFor(() => {
      expect(api.calculatePrice).toHaveBeenCalledWith(
        expect.objectContaining({
          selectedServices: ['umzug'],
          movingDetails: expect.objectContaining({
            apartmentSize: 80
          })
        })
      )
    })
  })

  it('displays price breakdown correctly', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Navigate through the flow to price summary
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })
    
    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    // Fill minimal required fields
    await waitFor(() => {
      expect(screen.getByLabelText(/Wohnungsgröße/)).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Wohnungsgröße/), '80')
    await user.type(screen.getByLabelText(/Von Adresse/), 'Berlin')
    await user.type(screen.getByLabelText(/Nach Adresse/), 'München')
    await user.click(screen.getByText('Weiter'))

    // Should show price summary
    await waitFor(() => {
      expect(screen.getByText('450,00€')).toBeInTheDocument()
      expect(screen.getByText('Umzug Grundpreis')).toBeInTheDocument()
      expect(screen.getByText('Entfernungszuschlag')).toBeInTheDocument()
    })
  })

  it('submits quote request with contact information', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    // Navigate to contact form (simplified flow)
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
    await user.click(screen.getByText('Weiter'))

    // Proceed to contact form
    await waitFor(() => {
      expect(screen.getByText('Kostenlose Beratung anfordern')).toBeInTheDocument()
    })
    
    await user.click(screen.getByText('Kostenlose Beratung anfordern'))

    // Fill contact information
    await waitFor(() => {
      expect(screen.getByLabelText(/Name/)).toBeInTheDocument()
    })

    await user.type(screen.getByLabelText(/Name/), 'Max Mustermann')
    await user.type(screen.getByLabelText(/E-Mail/), 'max@example.com')
    await user.type(screen.getByLabelText(/Telefon/), '+49 123 456789')

    // Submit quote
    await user.click(screen.getByText('Angebot anfordern'))

    // Should call submit API
    await waitFor(() => {
      expect(api.submitQuote).toHaveBeenCalledWith(
        expect.objectContaining({
          name: 'Max Mustermann',
          email: 'max@example.com',
          phone: '+49 123 456789'
        })
      )
    })
  })

  it('handles API errors gracefully', async () => {
    const user = userEvent.setup()
    
    // Mock API error
    api.calculatePrice.mockRejectedValue(new Error('Network error'))
    
    render(<MockedCalculator />)

    // Navigate to calculation step
    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })
    
    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Weiter'))

    // Fill details and try to calculate
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
  })

  it('supports multiple service selection', async () => {
    const user = userEvent.setup()
    render(<MockedCalculator />)

    await waitFor(() => {
      expect(screen.getByText('Umzug')).toBeInTheDocument()
    })

    // Select multiple services
    await user.click(screen.getByText('Umzug'))
    await user.click(screen.getByText('Putzservice'))

    // Should show both services as selected
    expect(screen.getByText('Umzug')).toHaveClass('selected') // Assuming selected class exists
    expect(screen.getByText('Putzservice')).toHaveClass('selected')

    await user.click(screen.getByText('Weiter'))

    // Should show moving details first
    await waitFor(() => {
      expect(screen.getByText('Umzug Details')).toBeInTheDocument()
    })
  })
})