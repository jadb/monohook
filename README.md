# Monohook - Git hooks in PHP

A re-visited approach to [Git hooks][githooks] - and probably other like Mercurial or Subversion?

## Install

Monohook can be installed using [Composer][composer]:

```
composer require --dev jadb/monohook:dev-master
```

## Usage

Now that you have the package installed, you just need to create your hooks and symlink them to
the `.monohook` config you create.

To create your `.monohook`, please refer to the one used for this repository.

Now, symlink the hooks you want managed by monohook:

```
$ ln -s ../../.monohook .git/hooks/pre-commit
$ ln -s ../../.monohook .git/hooks/post-commit
$ ln -s ../../.monohook .git/hooks/pre-rebase
```

## What's included?

### Processors

* __CodeSnifferProcessor__: Detect violations of a defined set of coding standards in PHP and CSS.
* __LintProcessor__: Syntax check every new PHP file.
* __PHPUnitProcessor__: Test (when possible) every new PHP file.
* __RebaseProcessor__: Make sure that a rebase does not overwrite already pushed commits.
* _TODO_ __ContentFilterProcessor__: Check any new code for filterable content (debug, wording, etc.).
* _TODO_ __ImageOptimizerProcessor__: Reduce images' sizes.

### Providers

* __GitProvider__
* _TODO_ __MercurialProvider__
* _TODO_ __SubversionProvider__

### Handlers

* __StdoutHandler__
* _TODO_ __EmailHandler__
* _TODO_ __StreamHandler__

## Contributing

* Fork
* Mod, fix, test
* _Optionally_ write some documentation (currently in `README.md`)
* Send pull request

All contributed code must be licensed under the [BSD 3-Clause License][bsd3clause].

## Bugs & Feedback

http://github.com/jadb/monohook/issues

## License

Copyright (c) 2015, [Jad Bitar][jadbio]

Licensed under the [BSD 3-Clause License][bsd3clause]
Redistributions of files must retain the above copyright notice.

## Acknowledgements

Originally inspired by [AD7six/git-hooks][AD7six/git-hooks] and [Seldaek/monolog][Seldaek/monolog].

[jadbio]:http://jadb.io
[bsd3clause]:http://opensource.org/licenses/BSD-3-Clause
[githooks]:http://git-scm.com/book/en/Customizing-Git-Git-Hooks‎
[composer]:http://getcomposer.org
[sample]:http://github.com/jadb/monohook/blob/master/examples/git-monohook/monohook.sample
[script]:http://github.com/jadb/monohook/blob/master/examples/git-monohook/git-monohook
[AD7six/git-hooks]:https://github.com/AD7six/git-hooks
[Seldaek/monolog]:https://github.com/Seldaek/monolog
