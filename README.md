Bounce
======
[![Build Status](https://travis-ci.org/moodev/bounce.png)](https://travis-ci.org/moodev/bounce)

Bounce is a dependency injection framework for PHP, similar to a sub-set of Spring.

Usage
=====
Create a context XML file (see some of the examples in the tests.)
Instantiate an ApplicationContext implementation pointed at the context XML.
Call the ApplicationContext's get method with bean names.
Receive beans.
...
profit.

What's supported
================
Only the basics.
Setter and constructor injection work.
Beans can be constructed from factories.
Lookup methods work, but only if the bean doesn't get created by a factory.
Scoping is really basic. Singleton is the default. Prototype is, in theory, supported.

