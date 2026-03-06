/**
 * Quote Module Entry Point
 * Registers the quoteManager Alpine.js component globally.
 */
import { quoteManager } from './QuoteManager.js';

// Expose globally so Blade x-data="quoteManager(...)" works
window.quoteManager = quoteManager;
