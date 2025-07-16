import { buildConfig } from 'payload/config';
import { mongooseAdapter } from '@payloadcms/db-mongodb';
import { webpackBundler } from '@payloadcms/bundler-webpack';
import { slateEditor } from '@payloadcms/richtext-slate';
import path from 'path';

export default buildConfig({
  admin: {
    user: 'users',
    bundler: webpackBundler(),
  },
  editor: slateEditor({}),
  collections: [
    // User management
    {
      slug: 'users',
      auth: true,
      admin: {
        useAsTitle: 'email',
      },
      fields: [
        {
          name: 'role',
          type: 'select',
          options: [
            { label: 'Admin', value: 'admin' },
            { label: 'Editor', value: 'editor' },
          ],
          defaultValue: 'editor',
          required: true,
        },
      ],
    },
    // Quote Requests Management
    {
      slug: 'quote-requests',
      admin: {
        useAsTitle: 'email',
        defaultColumns: ['email', 'services', 'totalPrice', 'status', 'createdAt'],
      },
      fields: [
        {
          name: 'status',
          type: 'select',
          options: [
            { label: 'Neu', value: 'new' },
            { label: 'In Bearbeitung', value: 'processing' },
            { label: 'Angebot gesendet', value: 'quoted' },
            { label: 'Abgeschlossen', value: 'completed' },
            { label: 'Abgelehnt', value: 'rejected' },
          ],
          defaultValue: 'new',
          required: true,
        },
        {
          name: 'email',
          type: 'email',
          required: true,
        },
        {
          name: 'phone',
          type: 'text',
        },
        {
          name: 'name',
          type: 'text',
        },
        {
          name: 'services',
          type: 'array',
          fields: [
            {
              name: 'type',
              type: 'select',
              options: [
                { label: 'Umzug', value: 'moving' },
                { label: 'Entrümpelung', value: 'declutter' },
                { label: 'Putzservice', value: 'cleaning' },
              ],
            },
            {
              name: 'details',
              type: 'json',
            },
          ],
        },
        {
          name: 'totalPrice',
          type: 'number',
        },
        {
          name: 'priceBreakdown',
          type: 'json',
        },
        {
          name: 'notes',
          type: 'textarea',
        },
        {
          name: 'adminNotes',
          type: 'textarea',
          admin: {
            description: 'Interne Notizen (nicht für Kunden sichtbar)',
          },
        },
      ],
    },
    // Services Management
    {
      slug: 'services',
      admin: {
        useAsTitle: 'name',
      },
      fields: [
        {
          name: 'name',
          type: 'text',
          required: true,
        },
        {
          name: 'slug',
          type: 'text',
          required: true,
          unique: true,
        },
        {
          name: 'description',
          type: 'richText',
        },
        {
          name: 'basePrice',
          type: 'number',
          required: true,
        },
        {
          name: 'pricePerKm',
          type: 'number',
        },
        {
          name: 'pricePerHour',
          type: 'number',
        },
        {
          name: 'pricePerM3',
          type: 'number',
        },
        {
          name: 'active',
          type: 'checkbox',
          defaultValue: true,
        },
        {
          name: 'options',
          type: 'array',
          fields: [
            {
              name: 'name',
              type: 'text',
              required: true,
            },
            {
              name: 'price',
              type: 'number',
              required: true,
            },
            {
              name: 'description',
              type: 'text',
            },
          ],
        },
      ],
    },
    // Website Content Management
    {
      slug: 'pages',
      admin: {
        useAsTitle: 'title',
      },
      fields: [
        {
          name: 'title',
          type: 'text',
          required: true,
        },
        {
          name: 'slug',
          type: 'text',
          required: true,
          unique: true,
        },
        {
          name: 'content',
          type: 'richText',
        },
        {
          name: 'seoTitle',
          type: 'text',
        },
        {
          name: 'seoDescription',
          type: 'textarea',
        },
        {
          name: 'published',
          type: 'checkbox',
          defaultValue: false,
        },
      ],
    },
    // Email Templates
    {
      slug: 'email-templates',
      admin: {
        useAsTitle: 'name',
      },
      fields: [
        {
          name: 'name',
          type: 'text',
          required: true,
        },
        {
          name: 'slug',
          type: 'text',
          required: true,
          unique: true,
        },
        {
          name: 'subject',
          type: 'text',
          required: true,
        },
        {
          name: 'htmlContent',
          type: 'richText',
        },
        {
          name: 'textContent',
          type: 'textarea',
        },
        {
          name: 'variables',
          type: 'array',
          fields: [
            {
              name: 'name',
              type: 'text',
              required: true,
            },
            {
              name: 'description',
              type: 'text',
            },
          ],
        },
      ],
    },
  ],
  globals: [
    // Site Settings
    {
      slug: 'site-settings',
      fields: [
        {
          name: 'calculatorEnabled',
          type: 'checkbox',
          defaultValue: true,
          admin: {
            description: 'Kostenrechner aktivieren/deaktivieren',
          },
        },
        {
          name: 'maintenanceMode',
          type: 'checkbox',
          defaultValue: false,
          admin: {
            description: 'Wartungsmodus aktivieren',
          },
        },
        {
          name: 'contactEmail',
          type: 'email',
          required: true,
        },
        {
          name: 'contactPhone',
          type: 'text',
        },
        {
          name: 'companyName',
          type: 'text',
          required: true,
        },
        {
          name: 'companyAddress',
          type: 'textarea',
        },
        {
          name: 'cookieBanner',
          type: 'group',
          fields: [
            {
              name: 'enabled',
              type: 'checkbox',
              defaultValue: true,
            },
            {
              name: 'text',
              type: 'richText',
              defaultValue: 'Wir verwenden Cookies und ähnliche Technologien, um unsere Website sicher und zuverlässig zu betreiben, Inhalte und Anzeigen zu personalisieren sowie die Zugriffe auf unsere Website zu analysieren. Einige Cookies sind notwendig, andere dienen statistischen Zwecken oder der Anzeige personalisierter Inhalte.',
            },
          ],
        },
      ],
    },
    // Legal Pages
    {
      slug: 'legal-content',
      fields: [
        {
          name: 'agb',
          type: 'richText',
          admin: {
            description: 'Allgemeine Geschäftsbedingungen',
          },
        },
        {
          name: 'datenschutz',
          type: 'richText',
          admin: {
            description: 'Datenschutzerklärung',
          },
        },
        {
          name: 'impressum',
          type: 'richText',
          admin: {
            description: 'Impressum',
          },
        },
      ],
    },
  ],
  typescript: {
    outputFile: path.resolve(__dirname, 'payload-types.ts'),
  },
  graphQL: {
    schemaOutputFile: path.resolve(__dirname, 'generated-schema.graphql'),
  },
  db: mongooseAdapter({
    url: process.env.DATABASE_URI || 'mongodb://localhost:27017/umzug-cms',
  }),
});