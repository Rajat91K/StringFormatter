# StringFormatter Class - Complete Documentation & Examples

## Table of Contents
- [Introduction](#introduction)
- [Installation](#installation)
- [Basic Concepts](#basic-concepts)
- [Method Reference](#method-reference)
- [Usage Examples](#usage-examples)
  - [Basic Usage](#basic-usage)
  - [Address Formatting](#address-formatting)
  - [Styled Content](#styled-content)
  - [Business Documents](#business-documents)
  - [E-commerce](#e-commerce)
  - [Reports & Data Display](#reports--data-display)
  - [User Profiles & Cards](#user-profiles--cards)
  - [Notifications & Messages](#notifications--messages)
  - [Advanced Techniques](#advanced-techniques)
- [Common Patterns & Best Practices](#common-patterns--best-practices)
- [Tips & Tricks](#tips--tricks)
- [Troubleshooting](#troubleshooting)

---

## Introduction

`StringFormatter` is a PHP class designed to build formatted strings with support for HTML output, inline styling, automatic empty value handling, and smart delimiter cleaning.

### Key Features
- ✅ Fluent/chainable API
- ✅ Automatic empty value checking
- ✅ Smart delimiter cleaning (no trailing commas)
- ✅ HTML mode with inline CSS support
- ✅ Conditional content addition
- ✅ Section/container support
- ✅ Multiple output formats
- ✅ XSS protection

---

## Installation

Simply include the `StringFormatter.php` file in your project:

```php
require_once 'path/to/StringFormatter.php';
```

---

## Basic Concepts

### 1. Create Instance
```php
$formatter = StringFormatter::create();
```

### 2. Enable HTML Mode
```php
$formatter->htmlMode(true);
```

### 3. Add Content (Chainable)
```php
$formatter->addLine('Content')->addBreak()->addBold('Bold text');
```

### 4. Generate Output
```php
$result = $formatter->toString();
// or simply:
$result = (string)$formatter;
```

---

## Method Reference

### Core Methods

| Method | Description | Example |
|--------|-------------|---------|
| `create()` | Static factory method | `StringFormatter::create()` |
| `htmlMode($enabled)` | Enable/disable HTML mode | `->htmlMode(true)` |
| `toString()` | Convert to string | `->toString()` |
| `clear()` | Clear all content | `->clear()` |
| `isEmpty()` | Check if empty | `->isEmpty()` |
| `count()` | Get number of parts | `->count()` |

### Content Adding Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `add($value, $prefix, $separator)` | value, optional prefix, separator | Add value with optional prefix |
| `addRaw($value)` | value | Add raw value without processing |
| `addLine($value, $prefix, $separator)` | value, optional prefix, separator | Add value and break |
| `addRawLine($value)` | value | Add raw value and break |
| `addBold($value, $class, $style)` | value, optional class, style | Add bold text |
| `addBoldLine($value, $class, $style)` | value, optional class, style | Add bold text and break |
| `addBreak()` | none | Add line break |
| `addBreakIf($condition)` | condition | Conditional break |

### Inline Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `addInline($data, $keys, $delimiter, $separator)` | data array, keys array, optional delimiter, separator | Add multiple values on same line |
| `addInlineLine($data, $keys, $delimiter, $separator)` | data array, keys array, optional delimiter, separator | Add inline values and break |
| `addMultiple($data, $keys, $prefix, $separator)` | data array, keys array, optional prefix, separator | Add multiple values, each on new line |

### Conditional Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `addIf($condition, $value, $prefix, $separator)` | condition, value, optional prefix, separator | Add only if condition is true |
| `addFromArray($data, $key, $prefix, $separator)` | data array, key, optional prefix, separator | Safely add from array |
| `addLineFromArray($data, $key, $prefix, $separator)` | data array, key, optional prefix, separator | Safely add from array with break |

### Styling Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `addDiv($content, $class, $style)` | content, optional class, style | Wrap in div |
| `addSpan($content, $class, $style)` | content, optional class, style | Wrap in span |
| `addStyledLine($content, $class, $style)` | content, optional class, style | Add styled div |
| `addStyled($content, $class, $style)` | content, optional class, style | Add styled span |
| `addTag($tag, $content, $class, $style)` | tag name, content, optional class, style | Add custom HTML tag |
| `addSection($title, $content, $class, $style)` | title, optional content, class, style | Add section header |
| `startSection($class, $style)` | optional class, style | Start container div |
| `endSection()` | none | End container div |

### Delimiter Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `withDelimiter($delimiter)` | delimiter string | Set custom delimiter |
| `withLineDelimiter()` | none | Use line break as delimiter |

### Advanced Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `split()` | none | Mark section boundary |
| `buildSeparate()` | none | Return array of sections |
| `buildCombined($separator)` | optional separator | Combine sections with separator |

---

## Usage Examples

### Basic Usage

#### Example 1: Simple Lines
```php
$text = StringFormatter::create()
    ->htmlMode(true)
    ->addLine('First line')
    ->addLine('Second line')
    ->addLine('Third line')
    ->toString();

// Output:
// First line<br>Second line<br>Third line
```

#### Example 2: Values with Labels
```php
$info = StringFormatter::create()
    ->htmlMode(true)
    ->addLine('John Doe', 'Name')
    ->addLine('john@example.com', 'Email')
    ->addLine('9876543210', 'Phone')
    ->toString();

// Output:
// Name: John Doe<br>
// Email: john@example.com<br>
// Phone: 9876543210
```

#### Example 3: Inline Values
```php
$location = StringFormatter::create()
    ->htmlMode(true)
    ->addInlineLine(['city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India'], 
        ['city', 'state', 'country'])
    ->toString();

// Output: Mumbai, Maharashtra, India
```

#### Example 4: Bold Text
```php
$heading = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine('Important Heading')
    ->addLine('Regular content below')
    ->toString();

// Output:
// <strong>Important Heading</strong><br>
// Regular content below
```

---

### Address Formatting

#### Example 5: Basic Address
```php
$address = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine('ABC Corporation')
    ->addLine('123 Main Street')
    ->addLine('Building A, Floor 5')
    ->addInlineLine(['city' => 'Mumbai', 'state' => 'Maharashtra', 'pincode' => '400001'], 
        ['city', 'state', 'pincode'])
    ->toString();

// Output:
// <strong>ABC Corporation</strong><br>
// 123 Main Street<br>
// Building A, Floor 5<br>
// Mumbai, Maharashtra, 400001
```

#### Example 6: Compact Address
```php
$broker = [
    'account_name' => 'XYZ Ltd',
    'address_line1' => '456 Park Avenue',
    'address_line2' => 'Suite 200',
    'city' => 'Delhi',
    'state_name' => 'Delhi',
    'pincode' => '110001'
];

$address = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine($broker['account_name'])
    ->addInlineLine($broker, ['address_line1', 'address_line2'])
    ->addInlineLine($broker, ['city', 'state_name', 'pincode'])
    ->toString();

// Output:
// <strong>XYZ Ltd</strong><br>
// 456 Park Avenue, Suite 200<br>
// Delhi, Delhi, 110001
```

#### Example 7: Address with Contact Information
```php
$broker = [
    'account_name' => 'ABC Corporation',
    'address_line1' => '123 Main St',
    'address_line2' => 'Building A',
    'city' => 'Mumbai',
    'state_name' => 'Maharashtra',
    'pin_no' => '400001',
    'mobile_no' => '9876543210',
    'pan_no' => 'ABCDE1234F',
    'gst_no' => '27ABCDE1234F1Z5'
];

$broker_details = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine($broker['account_name'])
    ->addInline($broker, ['address_line1', 'address_line2', 'city', 'state_name'])
    ->addRaw(isset($broker['pin_no']) && $broker['pin_no'] ? ', PIN-' : '')
    ->addRaw($broker['pin_no'] ?? '')
    ->addBreak()
    ->addInlineLine($broker, [
        'mobile_no' => 'M',
        'pan_no' => 'PAN',
        'gst_no' => 'GSTIN'
    ])
    ->toString();

// Output:
// <strong>ABC Corporation</strong><br>
// 123 Main St, Building A, Mumbai, Maharashtra, PIN-400001<br>
// M: 9876543210, PAN: ABCDE1234F, GSTIN: 27ABCDE1234F1Z5
```

#### Example 8: Conditional Address Fields
```php
$customer = [
    'name' => 'John Doe',
    'address_line1' => '789 Oak Street',
    'address_line2' => '',  // Empty
    'city' => 'Bangalore',
    'state' => 'Karnataka',
    'zipcode' => '560001',
    'country' => 'India'
];

$address = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine($customer['name'])
    ->addLine($customer['address_line1'])
    ->addIf(!empty($customer['address_line2']), $customer['address_line2'])
    ->addInlineLine($customer, ['city', 'state', 'zipcode'])
    ->addIf(!empty($customer['country']), $customer['country'])
    ->toString();

// Output:
// <strong>John Doe</strong><br>
// 789 Oak Street<br>
// Bangalore, Karnataka, 560001<br>
// India
// (address_line2 is skipped because it's empty)
```

---

### Styled Content

#### Example 9: Different Font Sizes
```php
$broker_details = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'font-size: 12px;')
        ->addBold($broker['account_name'], null, 'font-size: 14px;')
        ->addBreak()
        ->addInlineLine($broker, ['address_line1', 'city', 'state_name'])
        ->addInlineLine($broker, [
            'mobile_no' => 'M',
            'pan_no' => 'PAN',
            'gst_no' => 'GSTIN'
        ])
    ->endSection()
    ->toString();

// Output:
// <div style="font-size: 12px;">
// <strong style="font-size: 14px;">ABC Corp</strong><br>
// 123 Main St, Mumbai, Maharashtra<br>
// M: 9876543210, PAN: ABCDE1234F, GSTIN: 27ABC1234F1Z5
// </div>
```

#### Example 10: Colored Sections
```php
$notification = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'background: #d4edda; border-left: 4px solid #28a745; padding: 15px; color: #155724;')
        ->addBold('✓ Success!', null, 'font-size: 16px;')
        ->addBreak()
        ->addRaw('Your order has been placed successfully.')
    ->endSection()
    ->toString();

// Output:
// <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; color: #155724;">
// <strong style="font-size: 16px;">✓ Success!</strong><br>
// Your order has been placed successfully.
// </div>
```

---

### Business Documents

#### Example 11: Invoice Header
```php
$invoice = [
    'invoice_no' => 'INV-2024-001',
    'date' => '21-Oct-2025',
    'due_date' => '20-Nov-2025'
];

$customer = [
    'name' => 'ABC Corporation',
    'address' => '123 Main Street',
    'city' => 'Mumbai',
    'state' => 'Maharashtra'
];

$invoice_header = StringFormatter::create()
    ->htmlMode(true)
    ->addBold('INVOICE', null, 'font-size: 24px; color: #2c3e50;')
    ->addBreak()
    ->addBreak()
    ->addLine($invoice['invoice_no'], 'Invoice No')
    ->addLine($invoice['date'], 'Date')
    ->addLine($invoice['due_date'], 'Due Date')
    ->addBreak()
    ->addBoldLine('Bill To:', null, 'font-size: 14px;')
    ->addLine($customer['name'])
    ->addInlineLine($customer, ['address', 'city', 'state'])
    ->toString();
```

#### Example 12: Business Card
```php
$employee = [
    'name' => 'Rajesh Kumar',
    'designation' => 'Senior Manager',
    'company_name' => 'Tech Solutions Pvt Ltd',
    'department' => 'Sales',
    'mobile' => '9876543210',
    'email' => 'rajesh@techsolutions.com',
    'website' => 'www.techsolutions.com'
];

$business_card = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'border: 2px solid #2c3e50; padding: 20px; border-radius: 10px; max-width: 350px;')
        ->addBold($employee['name'], null, 'font-size: 18px; color: #2c3e50;')
        ->addBreak()
        ->addStyled($employee['designation'], null, 'font-size: 13px; color: #7f8c8d; font-style: italic;')
        ->addBreak()
        ->addBreak()
        ->addBold($employee['company_name'], null, 'color: #3498db;')
        ->addBreak()
        ->addLine($employee['department'], 'Department')
        ->addBreak()
        ->addLine($employee['mobile'], '📱')
        ->addLine($employee['email'], '✉️')
        ->addLine($employee['website'], '🌐')
    ->endSection()
    ->toString();
```

---

### E-commerce

#### Example 13: Product Card
```php
$product = [
    'name' => 'Premium Wireless Headphones',
    'description' => 'High-quality sound with active noise cancellation',
    'sku' => 'WH-1000XM5',
    'category' => 'Electronics',
    'brand' => 'Sony',
    'price' => 29999.00,
    'original_price' => 34999.00
];

$product_card = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine($product['name'], null, 'font-size: 16px; color: #2c3e50;')
    ->addLine($product['description'])
    ->addBreak()
    ->addInlineLine($product, [
        'sku' => 'SKU',
        'category' => 'Category',
        'brand' => 'Brand'
    ], ' | ')
    ->addBreak()
    ->addBold('₹' . number_format($product['price'], 2), null, 'font-size: 20px; color: #27ae60;')
    ->addRaw(' ')
    ->addStyled('₹' . number_format($product['original_price'], 2), null, 'text-decoration: line-through; color: #95a5a6; font-size: 14px;')
    ->toString();
```

#### Example 14: Order Item
```php
$item = [
    'product_name' => 'Apple iPhone 15 Pro',
    'quantity' => 2,
    'sku' => 'IP15P-256-BLK',
    'size' => '256GB',
    'color' => 'Black',
    'price' => 134900.00,
    'total' => 269800.00
];

$order_item = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px;')
        ->addBold($item['product_name'], null, 'font-size: 14px;')
        ->addRaw(' x ' . $item['quantity'])
        ->addBreak()
        ->addInline(['SKU', 'Size', 'Color'], [$item['sku'], $item['size'], $item['color']], ' | ')
        ->addBreak()
        ->addBold('Price: ', null, 'color: #27ae60;')
        ->addRaw('₹' . number_format($item['price'], 2))
        ->addRaw(' | ')
        ->addBold('Total: ')
        ->addRaw('₹' . number_format($item['total'], 2))
    ->endSection()
    ->toString();
```

---

### Reports & Data Display

#### Example 15: Monthly Report Summary
```php
$data = [
    'total_sales' => '₹12,50,000',
    'total_orders' => '485',
    'avg_order_value' => '₹2,577',
    'new_customers' => '127'
];

$report = StringFormatter::create()
    ->htmlMode(true)
    ->addSection('Monthly Sales Report - October 2025', null, null, 'font-size: 20px; color: #2c3e50; margin-bottom: 20px;')
    ->startSection(null, 'background: #ecf0f1; padding: 15px; border-radius: 5px; margin-bottom: 15px;')
        ->addBoldLine('Summary', null, 'font-size: 16px;')
        ->addInlineLine($data, [
            'total_sales' => 'Sales',
            'total_orders' => 'Orders',
            'avg_order_value' => 'Avg Order',
            'new_customers' => 'New Customers'
        ], ' | ')
    ->endSection()
    ->toString();
```

#### Example 16: Data Table Cell
```php
// For use in HTML tables
function generateTableCell($broker) {
    return StringFormatter::create()
        ->htmlMode(true)
        ->startSection(null, 'font-size: 12px;')
            ->addBold($broker['account_name'], null, 'font-size: 14px;')
            ->addBreak()
            ->addInlineLine($broker, ['city', 'state_name'])
            ->addInlineLine($broker, [
                'mobile_no' => 'M',
                'gst_no' => 'GST'
            ])
        ->endSection()
        ->toString();
}

// Usage in loop
foreach ($brokers as $broker) {
    echo '<tr>';
    echo '<td>' . $broker['id'] . '</td>';
    echo '<td>' . generateTableCell($broker) . '</td>';
    echo '<td>' . $broker['status'] . '</td>';
    echo '</tr>';
}
```

---

### User Profiles & Cards

#### Example 17: User Profile Card
```php
$user = [
    'name' => 'Priya Sharma',
    'username' => '@priya_s',
    'bio' => 'Digital Marketing Specialist | Content Creator',
    'location' => 'Mumbai, India',
    'joined_date' => 'Jan 2023',
    'role' => 'Premium Member'
];

$profile = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0;')
        ->addBold($user['name'], null, 'font-size: 24px;')
        ->addBreak()
        ->addStyled($user['username'], null, 'font-size: 14px; opacity: 0.9;')
    ->endSection()
    ->startSection(null, 'background: #fff; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px;')
        ->addBoldLine('About', null, 'font-size: 14px; color: #34495e;')
        ->addLine($user['bio'])
        ->addBreak()
        ->addBoldLine('Details', null, 'font-size: 14px; color: #34495e;')
        ->addInlineLine($user, [
            'location' => 'Location',
            'joined_date' => 'Joined',
            'role' => 'Role'
        ])
    ->endSection()
    ->toString();
```

---

### Notifications & Messages

#### Example 18: Success Notification
```php
$notification = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'background: #d4edda; border: 1px solid #c3e6cb; border-left: 4px solid #28a745; padding: 15px; border-radius: 5px; color: #155724;')
        ->addBold('✓ Success!', null, 'font-size: 16px;')
        ->addBreak()
        ->addRaw('Your order has been placed successfully.')
        ->addBreak()
        ->addBreak()
        ->addLine('Order #ORD-2024-789', 'Order ID')
        ->addLine('₹15,999.00', 'Total')
        ->addLine('28-Oct-2025', 'Expected Delivery')
    ->endSection()
    ->toString();
```

#### Example 19: Error Message
```php
$error = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'background: #f8d7da; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; padding: 15px; border-radius: 5px; color: #721c24;')
        ->addBold('✗ Error', null, 'font-size: 16px;')
        ->addBreak()
        ->addRaw('Unable to process your payment.')
        ->addBreak()
        ->addLine('Please check your card details and try again.')
    ->endSection()
    ->toString();
```

---

### Advanced Techniques

#### Example 20: Using split() for Multiple Sections
```php
$formatter = StringFormatter::create()->htmlMode(true);

// Section 1: Billing Address
$formatter
    ->addBoldLine('Billing Address', null, 'font-size: 14px; color: #2c3e50;')
    ->addLine($billing['name'])
    ->addInlineLine($billing, ['address', 'city', 'state', 'zip'])
    ->split();

// Section 2: Shipping Address
$formatter
    ->addBoldLine('Shipping Address', null, 'font-size: 14px; color: #2c3e50;')
    ->addLine($shipping['name'])
    ->addInlineLine($shipping, ['address', 'city', 'state', 'zip'])
    ->split();

// Get as separate strings
$sections = $formatter->buildSeparate();
$billing_html = $sections[0];
$shipping_html = $sections[1];

// Or combine with custom separator
$combined = $formatter->buildCombined('<div style="margin: 20px 0; border-top: 1px solid #ddd;"></div>');
```

#### Example 21: Custom Delimiters
```php
// Using bullet points
$features = StringFormatter::create()
    ->htmlMode(true)
    ->withDelimiter(' • ')
    ->addBoldLine('Product Features')
    ->addInline(['Wireless', '30-hour battery', 'Noise cancellation', 'Bluetooth 5.0'], 
        [0, 1, 2, 3])
    ->toString();

// Output: 
// <strong>Product Features</strong><br>
// Wireless • 30-hour battery • Noise cancellation • Bluetooth 5.0

// Using pipes
$specs = StringFormatter::create()
    ->htmlMode(true)
    ->withDelimiter(' | ')
    ->addInlineLine($product, ['brand', 'model', 'year'])
    ->toString();

// Output: Apple | iPhone 15 | 2024
```

#### Example 22: Reusable Formatter Functions
```php
// Create reusable formatting functions
class FormatterHelpers {
    public static function formatAddress($data) {
        return StringFormatter::create()
            ->htmlMode(true)
            ->addBoldLine($data['name'] ?? 'Unknown')
            ->addInlineLine($data, ['address_line1', 'address_line2'])
            ->addInlineLine($data, ['city', 'state', 'zipcode'])
            ->toString();
    }
    
    public static function formatContactInfo($data) {
        return StringFormatter::create()
            ->htmlMode(true)
            ->addInlineLine($data, [
                'mobile' => 'M',
                'email' => 'E',
                'phone' => 'P'
            ])
            ->toString();
    }
    
    public static function formatPrice($amount, $currency = '₹') {
        return StringFormatter::create()
            ->htmlMode(true)
            ->addBold($currency . number_format($amount, 2), null, 'font-size: 18px; color: #27ae60;')
            ->toString();
    }
}

// Usage
$address_html = FormatterHelpers::formatAddress($customer);
$contact_html = FormatterHelpers::formatContactInfo($vendor);
$price_html = FormatterHelpers::formatPrice(15999.00);
```

---

## Common Patterns & Best Practices

### Pattern 1: Address with Optional Fields
```php
$address = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine($data['name'])
    ->addLine($data['address_line1'])
    ->addIf(!empty($data['address_line2']), $data['address_line2'])
    ->addInlineLine($data, ['city', 'state', 'zipcode'])
    ->addIf(!empty($data['country']), $data['country'])
    ->toString();
```

### Pattern 2: Contact Information Block
```php
$contact = StringFormatter::create()
    ->htmlMode(true)
    ->addBoldLine('Contact Information')
    ->addInlineLine($data, [
        'mobile' => 'M',
        'phone' => 'P',
        'email' => 'E',
        'fax' => 'F'
    ])
    ->toString();
```

### Pattern 3: Styled Container
```php
$card = StringFormatter::create()
    ->htmlMode(true)
    ->startSection(null, 'border: 1px solid #ddd; padding: 15px; border-radius: 5px;')
        ->addBoldLine('Title', null, 'font-size: 16px;')
        ->addLine('Content goes here')
    ->endSection()
    ->toString();
```

### Pattern 4: Price Display with Original/Discounted
```php
$price = StringFormatter::create()
    ->htmlMode(true)
    ->addBold('₹' . number_format($product['price'], 2), null, 'font-size: 20px; color: #27ae60;')
    ->addRaw(' ')
    ->addStyled('₹' . number_format($product['original_price'], 2), null, 'text-decoration: line-through; color: #95a5a6;')
    ->toString();
```

---

## Tips & Tricks

1. **Always enable HTML mode for web output:**
   ```php
   ->htmlMode(true)
   ```

2. **Use associative arrays for prefixes:**
   ```php
   ->addInlineLine($data, ['mobile' => 'M', 'email' => 'E'])
   // Better than manually adding each field
   ```

3. **Leverage automatic empty checking:**
   ```php
   // No need to check if values exist
   ->addInlineLine($broker, ['address_line1', 'address_line2'])
   // Empty fields are automatically skipped
   ```

4. **Use startSection/endSection for containers:**
   ```php
   ->startSection(null, 'padding: 20px; background: #f5f5f5;')
       // content
   ->endSection()
   ```

5. **Chain methods for readability:**
   ```php
   StringFormatter::create()
       ->htmlMode(true)
       ->addBoldLine('Title')
       ->addLine('Content')
       ->toString();
   ```

6. **Use split() for separate sections:**
   ```php
   $formatter->addLine('Section 1')->split()->addLine('Section 2');
   $sections = $formatter->buildSeparate(); // Returns array
   ```

7. **Store formatters in functions for reusability:**
   ```php
   function formatAddress($data) {
       return StringFormatter::create()->htmlMode(true)
           ->addBoldLine($data['name'])
           ->addInlineLine($data, ['address', 'city', 'state'])
           ->toString();
   }
   ```

---

## Troubleshooting

### Issue: Extra line breaks
**Solution:** Use `addBold()` instead of `addBoldLine()` if you don't want a break after

### Issue: Empty lines appearing
**Solution:** The formatter automatically skips empty values, but if you're seeing empty lines, check if you're adding raw breaks

### Issue: Trailing commas
**Solution:** This is automatically handled by the `autoClean()` feature (enabled by default)

### Issue: Container div appears empty
**Solution:** Make sure content is added between `startSection()` and `endSection()`

### Issue: Styles not applying
**Solution:** Ensure `htmlMode(true)` is called and parameters are in correct order: `addBold($value, $class, $style)`

---

## Quick Reference

### Most Common Methods

```php
// Create and setup
StringFormatter::create()->htmlMode(true)

// Add content
->addLine($value, 'Label')              // Add with label
->addBoldLine($value)                    // Bold text with break
->addInlineLine($data, ['key1', 'key2']) // Multiple values, same line

// Add with prefixes
->addInlineLine($data, [
    'mobile' => 'M',
    'email' => 'E'
])

// Conditional
->addIf($condition, $value)

// Styling
->addBold($value, null, 'font-size: 14px;')
->startSection(null, 'padding: 10px;')
    // content
->endSection()

// Output
->toString()
```

---

**End of Documentation**

---

**Created:** October 2025  
**Version:** 4.0
