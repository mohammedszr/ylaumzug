/**
 * API utility for communicating with Laravel backend
 */

import { payloadAPI } from './payload-api';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

class ApiError extends Error {
  constructor(message, status, data = null) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.data = data;
  }
}

/**
 * Make HTTP request to Laravel API
 */
async function apiRequest(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const config = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    },
    ...options,
  };

  // Add CSRF token if available (for Laravel Sanctum)
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    config.headers['X-CSRF-TOKEN'] = csrfToken;
  }
  
  // Add X-Requested-With header for Laravel
  config.headers['X-Requested-With'] = 'XMLHttpRequest';

  try {
    const response = await fetch(url, config);
    
    // Handle non-JSON responses
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      throw new ApiError(
        'Server returned non-JSON response',
        response.status,
        { contentType }
      );
    }

    const data = await response.json();

    if (!response.ok) {
      throw new ApiError(
        data.message || `HTTP ${response.status}`,
        response.status,
        data
      );
    }

    return data;
  } catch (error) {
    if (error instanceof ApiError) {
      throw error;
    }

    // Network or other errors
    if (error.name === 'TypeError' && error.message.includes('fetch')) {
      throw new ApiError(
        'Netzwerkfehler. Bitte überprüfen Sie Ihre Internetverbindung.',
        0,
        { originalError: error.message }
      );
    }

    throw new ApiError(
      'Ein unerwarteter Fehler ist aufgetreten.',
      0,
      { originalError: error.message }
    );
  }
}

/**
 * Calculator API functions
 */
export const calculatorApi = {
  /**
   * Calculate pricing for selected services
   */
  async calculatePricing(calculatorData) {
    return apiRequest('/calculator/calculate', {
      method: 'POST',
      body: JSON.stringify({
        selectedServices: calculatorData.selectedServices,
        movingDetails: calculatorData.movingDetails,
        cleaningDetails: calculatorData.cleaningDetails,
        declutterDetails: calculatorData.declutterDetails,
        generalInfo: calculatorData.generalInfo,
      }),
    });
  },

  /**
   * Get available services from Payload CMS
   */
  async getServices() {
    try {
      const payloadServices = await payloadAPI.getServices();
      return payloadServices;
    } catch (error) {
      // Fallback to Laravel API
      console.warn('Payload CMS unavailable, falling back to Laravel API');
      return apiRequest('/calculator/services');
    }
  },

  /**
   * Check if calculator is enabled from Payload CMS
   */
  async isCalculatorEnabled() {
    try {
      const settings = await payloadAPI.getSiteSettings();
      return { enabled: settings.calculatorEnabled };
    } catch (error) {
      // Fallback to Laravel API
      console.warn('Payload CMS unavailable, falling back to Laravel API');
      return apiRequest('/calculator/enabled');
    }
  },
};

/**
 * Quote API functions
 */
export const quoteApi = {
  /**
   * Submit quote request
   */
  async submitQuote(quoteData) {
    try {
      // Store quote in Payload CMS for admin management
      const payloadQuote = await payloadAPI.createQuoteRequest({
        email: quoteData.email,
        phone: quoteData.phone,
        name: quoteData.name,
        services: quoteData.selectedServices,
        totalPrice: quoteData.totalPrice,
        priceBreakdown: quoteData.priceBreakdown,
        status: 'new',
      });

      // Also send to Laravel for email processing
      const laravelResponse = await apiRequest('/quotes/submit', {
        method: 'POST',
        body: JSON.stringify(quoteData),
      });

      return {
        ...laravelResponse,
        payloadId: payloadQuote.doc?.id,
      };
    } catch (error) {
      // If Payload fails, still try Laravel
      console.warn('Payload CMS unavailable for quote storage, using Laravel only');
      return apiRequest('/quotes/submit', {
        method: 'POST',
        body: JSON.stringify(quoteData),
      });
    }
  },
};

export { ApiError };