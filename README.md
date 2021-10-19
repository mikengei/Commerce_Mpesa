
# Commerce Mpesa

This is a Drupal 8 & 9 payment module for Drupal Commerce 2.x which has been integrated with Safaricom APIs to handle STK push only.

Currently this only for STK only, other services will be added later.


![App Screenshot](https://raw.githubusercontent.com/mikengei/commerce_mpesa/main/icons/img/Capture.PNG)

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

## Screenshots
* Add the Payment gateway
![App Screenshot](https://raw.githubusercontent.com/mikengei/commerce_mpesa/main/icons/img/pic1.PNG)

* Configure API settings.
![App Screenshot](https://raw.githubusercontent.com/mikengei/commerce_mpesa/main/icons/img/pic2.PNG)

* Add a view to display Payments made.
![App Screenshot](https://raw.githubusercontent.com/mikengei/commerce_mpesa/main/icons/img/pic3.PNG)

* Add fields to the view.
![App Screenshot](https://raw.githubusercontent.com/mikengei/commerce_mpesa/main/icons/img/pic4.PNG)
