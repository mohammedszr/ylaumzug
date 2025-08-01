import { describe, it, expect, vi, beforeEach } from 'vitest'
import * as api from '../lib/api'

// Mock fetch globally
global.fetch = vi.fn()

describe('API Functions', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    fetch.mockClear()
  })

  describe('calculatePrice', () => {
    it('sends correct data to calculate endpoint', async () => {
      const mockResponse = {
        success: true,
        pricing: {
          total: 450.00,
          breakdown: [
            { service: 'Umzug Grundpreis', price: 300.00 },
            { service: 'Entfernungszuschlag', price: 150.00 }
          ],
          currency: 'EUR'
        }
      }

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse
      })

      const requestData = {
        selectedServices: ['umzug'],
        movingDetails: {
          apartmentSize: 80,
          fromAddress: { postalCode: '10115' },
          toAddress: { postalCode: '10117' }
        }
      }

      const result = await api.calculatePrice(requestData)

      expect(fetch).toHaveBeenCalledWith(
        'http://localhost:8000/api/calculator/calculate',
        expect.objectContaining({
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(requestData)
        })
      )

      expect(result).toEqual(mockResponse)
    })

    it('handles API errors correctly', async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
        status: 422,
        json: async () => ({
          success: false,
          errors: {
            selectedServices: ['The selected services field is required.']
          }
        })
      })

      const requestData = { selectedServices: [] }

      await expect(api.calculatePrice(requestData)).rejects.toThrow('HTTP error! status: 422')
    })

    it('handles network errors', async () => {
      fetch.mockRejectedValueOnce(new Error('Network error'))

      const requestData = {
        selectedServices: ['umzug'],
        movingDetails: { apartmentSize: 80 }
      }

      await expect(api.calculatePrice(requestData)).rejects.toThrow('Network error')
    })
  })

  describe('submitQuote', () => {
    it('sends quote data to submit endpoint', async () => {
      const mockResponse = {
        success: true,
        message: 'Quote submitted successfully',
        quote_number: 'Q-2024-001'
      }

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse
      })

      const quoteData = {
        name: 'Max Mustermann',
        email: 'max@example.com',
        phone: '+49 123 456789',
        selectedServices: ['umzug'],
        movingDetails: {
          apartmentSize: 80,
          fromAddress: { street: 'Test Street 1', city: 'Berlin' },
          toAddress: { street: 'Test Street 2', city: 'Munich' }
        },
        estimatedTotal: 450.00
      }

      const result = await api.submitQuote(quoteData)

      expect(fetch).toHaveBeenCalledWith(
        'http://localhost:8000/api/quotes',
        expect.objectContaining({
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(quoteData)
        })
      )

      expect(result).toEqual(mockResponse)
    })

    it('validates required contact fields', async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
        status: 422,
        json: async () => ({
          success: false,
          errors: {
            name: ['The name field is required.'],
            email: ['The email field is required.']
          }
        })
      })

      const incompleteData = {
        phone: '+49 123 456789',
        selectedServices: ['umzug']
      }

      await expect(api.submitQuote(incompleteData)).rejects.toThrow('HTTP error! status: 422')
    })
  })

  describe('getServices', () => {
    it('fetches available services', async () => {
      const mockResponse = {
        success: true,
        services: [
          { key: 'umzug', name: 'Umzug', description: 'Moving service', base_price: 300 },
          { key: 'putzservice', name: 'Putzservice', description: 'Cleaning service', base_price: 150 },
          { key: 'entruempelung', name: 'Entrümpelung', description: 'Decluttering service', base_price: 300 }
        ]
      }

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse
      })

      const result = await api.getServices()

      expect(fetch).toHaveBeenCalledWith(
        'http://localhost:8000/api/calculator/services',
        expect.objectContaining({
          method: 'GET',
          headers: {
            'Accept': 'application/json'
          }
        })
      )

      expect(result).toEqual(mockResponse)
    })

    it('handles service fetch errors', async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
        status: 500,
        json: async () => ({
          success: false,
          message: 'Internal server error'
        })
      })

      await expect(api.getServices()).rejects.toThrow('HTTP error! status: 500')
    })
  })

  describe('API Configuration', () => {
    it('uses correct base URL', () => {
      expect(api.API_BASE_URL).toBe('http://localhost:8000/api')
    })

    it('includes proper headers in all requests', async () => {
      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ success: true })
      })

      await api.getServices()

      expect(fetch).toHaveBeenCalledWith(
        expect.any(String),
        expect.objectContaining({
          headers: expect.objectContaining({
            'Accept': 'application/json'
          })
        })
      )
    })
  })
})