# Magento 2 Product Redirect Module

A lightweight Magento 2 module that redirects product links to the correct localized product URLs based on SKU or Product ID.

## Problem

When managing a Magento 2 multi-store setup with different languages, product URLs differ across stores:

- English: `/awesome-product.html`
- French: `/produit-genial.html`
- German: `/tolles-produkt.html`

This module lets you use **one universal link** that automatically redirects to the correct localized product URL based on the current store.

## Requirements

- PHP >= 8.1
- Magento >= 2.4.7
- Composer (optional, for installation via composer)

## Installation

### Option 1: Install via Composer (Recommended)

```bash
composer require matusstafura/magento2-product-redirect
php bin/magento module:enable MatusStafura_ProductRedirect
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### Option 2: Manual Installation

1. Create module directory:
```bash
mkdir -p app/code/MatusStafura/ProductRedirect
```

2. Copy all module files to `app/code/MatusStafura/ProductRedirect/`

3. Enable the module:
```bash
php bin/magento module:enable MatusStafura_ProductRedirect
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## Usage

### Using SKU (Recommended)

Create links using product SKU:

```html
<a href="/product?sku=ABC123">Check out this product</a>
<a href="/product?sku=SHIRT-RED-L">Red Shirt - Large</a>
<a href="/product?sku=24-MB01">Joust Duffle Bag</a>
```

### Using Product ID

Create links using product ID:

```html
<a href="/product?id=12301">View Product</a>
<a href="/product?id=5847">Another Product</a>
```

## Use Cases

Perfect for:
- **Blog posts** shared across multiple store views
- **Email campaigns** sent to international customers
- **Social media** posts linking to products
- **Print materials** with QR codes
- **Affiliate links** that work globally
- **Internal documentation** referencing products

## SEO Benefits

- **301 permanent redirects** preserve link equity
- Redirects to **canonical product URLs** with proper localization
- Search engines credit the **final SEO-friendly URL**, not the redirect
- No duplicate content issues

## Configuration

No configuration needed! The module works out of the box after installation.

## Uninstallation

```bash
php bin/magento module:disable MatusStafura_ProductRedirect
php bin/magento setup:upgrade
php bin/magento cache:flush

# Optionally remove module files
rm -rf app/code/MatusStafura/ProductRedirect
```

## Compatibility

- ✅ Magento 2.4.7
- ✅ Magento 2.4.6
- ✅ Magento 2.4.5
- ✅ PHP 8.1, 8.2, 8.3

## License

MIT License - See [LICENSE](LICENSE) for details.

## Support

For issues, questions, or contributions create an issue on GitHub or submit a Pull Request.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

