# Hyperdrive

> The fastest way to load pages in WordPress.

![Hyperdrive Plugin for WordPress](https://github.com/comfusion/hyperdrive/blob/master/logo.png)

[![Build Status](https://travis-ci.org/comfusion/hyperdrive.svg?branch=master)](https://travis-ci.org/comfusion/hyperdrive)
[![Code Climate](https://codeclimate.com/github/comfusion/hyperdrive/badges/gpa.svg)](https://codeclimate.com/github/comfusion/hyperdrive)
[![Test Coverage](https://codeclimate.com/github/comfusion/hyperdrive/badges/coverage.svg)](https://codeclimate.com/github/comfusion/hyperdrive)

# Installation

Hyperdrive is [available on Packagist](https://packagist.org/packages/comfusion/hyperdrive).

- Installing with Composer: `composer require comfusion/hyperdrive`

# Documentation

Hyperdrive docs available online at:
http://hyperdrive.habd.as/

# Todo before 1.0.0 release

- [ ] Only dequeue scripts if [browser supports Fetch](http://caniuse.com/#search=fetch) for backwards compatibility with older browsers
- [ ] Integrate localization behaviors [as shown here](https://gist.github.com/jhabdas/64e8380010e43a526fb9c9ee511fad17#file-functions-php-L507).
- [ ] Test with a few different themes and open bugs and needed

# Post 1.0.0 roadmap

- [ ] Integrate User Interface created by [@wedangsu](https://github.com/wedangsusu)
- [ ] Ensure interface gives ability to defer script execution for scripts querying the DOM until after the DOM is fully parsed.
- [ ] Give ability to perform grouping, so non-jQuery scripts can download and execute without waiting for jQuery.
- [ ] Add ability to load icon fonts and [non-critical CSS](https://gist.github.com/scottjehl/87176715419617ae6994) (also possible with Fetch Inject)
- [ ] Build API enabling theme authors greater control

# How it works

Hyperdrive uses a performance optimization technique known as [Fetch Injection](https://hackcabin.com/post/managing-async-dependencies-javascript/), available in [browsers with support](http://caniuse.com/#search=fetch) for the Fetch API. Fetch is a modern replacement for Ajax.

# Contributing

Please open issues when creating PRs and PR against the issue to close it. This helps establish and separate a need (the issue) from the implementation (the pull), resulting in more robust solutions and ensuring current issues are considered before changes are requested.

Additionally:

- All code should follow WordPress [PHP Documentation Standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/).
- Pull requests should be linted prior to submission using the `lint` script in the Composer manifest file. If you have trouble running the linter ensure you have installed [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).

Though not required, if you plan on contributing code, please consider installing [EditorConfig](http://editorconfig.org/) for your editor or IDE to help normalize your code automagically.

# License

[GPL-3.0](https://opensource.org/licenses/GPL-3.0)
