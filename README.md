Bounce
======
[![Build Status](https://travis-ci.org/moodev/bounce.png)](https://travis-ci.org/moodev/bounce)

Bounce is a dependency injection framework for PHP, similar to a sub-set of Spring.
It used to be a standalone framework, which did a lot at runtime, and was therefore a bit expensive.
It's now a front end to Symfony2's DI library, which is a fair bit more developed than Bounce was.

Usage
=====

Create a context XML file (see some of the examples in the tests.)

Then:

New style using Symfony2
------------------------
Use the BounceFileLoader in your Symfony2 app. Both the BounceFileLoader and the Symfony XMLFileLoader will try and
claim all .xml files, which is less than ideal. You'll probably need to introduce some kind of delegating loader that
filters by path, or file name, or something, in front of them to prevent this. Sorry.

Old style ApplicationContext interface and configs
--------------------------------------------------
Instantiate a SymfonyApplicationContext. This will load the provided context into a new Symfony container and wrap it
in the old ApplicationContext interface.

If you're looking for a migration path away from the Bounce XML format, but have significant usage of the
ApplicationContext interface you could pass a different FileLoaderFactory to the SymfonyApplicationContext, which could
allow services to be loaded from other styles of Symfony config. Sadly the bounce xml's import statement does not
invoke the file loader, so bounce xml files cannot import things that aren't other bounce xml files.

ApplicationContext interface, externally provided container
-----------------------------------------------------------
The SymfonyContainerBeanFactory can wrap an existing container, and be used to instantiate the plain boring
ApplicationContext. This will give you the ApplicationContext interface, with none of the rest of bounce's config stuff.

(of course there's nothing stopping you using the BounceFileLoader to load your own container for this, which is
basically what the SymfonyApplicationContext is a shortcut to.)

What's supported
================
Only the basics.
Property and constructor injection work.
Beans can be constructed from factories.
Lookup methods work, but only if the bean doesn't get created by a factory.
Scoping is really basic. Singleton is the default. Prototype is, in theory, supported.

