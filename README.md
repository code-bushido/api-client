# API Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

API Client - PHP Library giving an abstraction for  

## Install

Via Composer

``` bash
$ composer require code-bushido/api-client
```

## Usage

``` php
$skeleton = new Bushido\ApiClient();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email wnowicki@me.com instead of using the issue tracker.

## Credits

- [Wojciech Nowicki][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/code-bushido/api-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/code-bushido/api-client/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/code-bushido/api-client.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/code-bushido/api-client.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/code-bushido/api-client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/code-bushido/api-client
[link-travis]: https://travis-ci.org/code-bushido/api-client
[link-scrutinizer]: https://scrutinizer-ci.com/g/code-bushido/api-client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/code-bushido/api-client
[link-downloads]: https://packagist.org/packages/code-bushido/api-client
[link-author]: https://github.com/wnowicki
[link-contributors]: ../../contributors
