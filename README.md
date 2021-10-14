
# Commerce Mpesa

This is a Drupal 8 & 9 payment module for Drupal Commerce 2.x which has been integrated with Safaricom APIs to handle STK push only.

Currently this only for STK only, other services will be added later.
## Installation

This module can be installed via Composer.

```bash
  composer require 'drupal/commerce_mpesa:^1.0'
```
With drush

```bash
  drush en commerce_mpesa -y
```

which will download and enable the module automatically.

For more information about installing Drupal Modules:

https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
https://www.drupal.org/docs/user_guide/en/extend-module-install.html
## Configuration

Create a new Commerce Mpesa gateway.
Administration > Commerce > Configuration > Payment gateways > Add payment gateway
Here are settings available:

- BusinessShortCode
- PassKey
- Basic Auth username
- Basic Auth password

Use the API credentials provided by your Mpesa merchant account.

## Features

- STK push
- IPN
- Payments dashboard for Mpesa
