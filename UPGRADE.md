# Upgrade guide

# 1.x to 2.x

2.x introduces support for multiple connections. To upgrade you must adjust your configuration file and OAuth urls.

## Configuration file

The configuration file has been changed to support multiple connections.
It is recommended to overwrite the version from the package so that you are up to date.

The environment variables did not change.
This means that if you have a single Magento connection you code will not break.

## OAuth

> [!NOTE]
> This step is only required when using OAuth authentication

Reauthorize your Magento integration to create the record in your DB.

### URL

The OAuth URL's now require a connection parameter, if you have a single Magento instance the connection value is `default` by default.

```
Callback URL:      https://example.com/magento/oauth/callback/{connection}
Identity link URL: https://example.com/magento/oauth/identity/{connection}
```

### Key Location

OAuth keys are now stored in the database by default.
If you prefer to store them on disk you can use the `\JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore` or implement your own keystore.


## Testing

The `Magento::fake()` method has been altered to use the URL `magento` instead of `http://magento.test`. You should replace this in your tests.
