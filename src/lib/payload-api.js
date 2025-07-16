const PAYLOAD_API_URL = import.meta.env.VITE_PAYLOAD_API_URL || 'http://localhost:3001/api';

class PayloadAPI {
  async request(endpoint, options = {}) {
    const url = `${PAYLOAD_API_URL}${endpoint}`;
    const config = {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return await response.json();
    } catch (error) {
      console.error('Payload API Error:', error);
      throw error;
    }
  }

  // Site Settings
  async getSiteSettings() {
    return this.request('/globals/site-settings');
  }

  // Services
  async getServices() {
    return this.request('/services?where[active][equals]=true');
  }

  async getService(slug) {
    return this.request(`/services?where[slug][equals]=${slug}&limit=1`);
  }

  // Quote Requests
  async createQuoteRequest(data) {
    return this.request('/quote-requests', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  // Email Templates
  async getEmailTemplate(slug) {
    return this.request(`/email-templates?where[slug][equals]=${slug}&limit=1`);
  }

  // Pages
  async getPage(slug) {
    return this.request(`/pages?where[slug][equals]=${slug}&where[published][equals]=true&limit=1`);
  }

  // Legal Content
  async getLegalContent() {
    return this.request('/globals/legal-content');
  }
}

export const payloadAPI = new PayloadAPI();