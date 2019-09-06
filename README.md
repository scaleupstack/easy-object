# ScaleUpStack/EasyObject

This library provides convenient object handling for a modern, defensive and strict style of programming in PHP.

This library reduces boilerplate code dramatically, and offers features that PHP does not support (yet), e.g.:

* structs,

* immutable objects,

* typed properties, including union types,

* default getters,

* simplified setup of test fixtures,

* no dynamic/un-defined properties in objects


This library is based on [scaleupstack/metadata] and [scaleupstack/reflection].


## Installation

Use [Composer] to install this library:

```
$ composer require scaleupstack/easy-object
```


## Introduction

The main motivation while developing this library, is to simplify modelling of your business domain in the sense of Domain-Driven Design (DDD), Event-Sourcing (ES) and Command Query Responsibility Segregation (CQRS). But of course, you are not limited to that.


Currently, you can enhance your objects with default features like:

* standard getters,

* standard constructors and named constructors (a.k.a. factory methods),

* typed properties, including union types, and

* a simplified and maintainable set-up of fixtures in your tests.

And it's very easy to implement additional features.

To understand the usage of the library, there are three things:

* The library offers magic behaviour, e.g. check-out [src/Magic/VirtualGetter.php](https://github.com/scaleupstack/easy-object/tree/master/src/Magic/VirtualGetter.php) or [src/Magic/NamedConstructor.php](https://github.com/scaleupstack/easy-object/tree/master/src/Magic/NamedConstructor.php).

* Next you need some trait where you include the relevant features, e.g. [src/Traits/FixtureBuilderTrait.php](https://github.com/scaleupstack/easy-object/tree/master/src/Traits/FixtureBuilderTrait.php). To get an idea, how to include static and non-static methods, checkout [tests/Resources/Magic/ClassForNamedConstructorTesting.php](https://github.com/scaleupstack/easy-object/tree/master/tests/Resources/Magic/ClassForNamedConstructorTesting.php). (Even the magic is not included as trait here, that is not a big deal.)

* Finally you need to annotate your entities and use the correct trait, e.g. as done in [tests/Resources/Traits/EntityForTesting.php](https://github.com/scaleupstack/easy-object/tree/master/tests/Resources/Traits/EntityForTesting.php), [tests/Resources/Traits/FixtureBuilderForTesting.php](https://github.com/scaleupstack/easy-object/tree/master/tests/Resources/Traits/FixtureBuilderForTesting.php), or [tests/Resources/Magic/ClassForDispatcherTesting.php](https://github.com/scaleupstack/easy-object/tree/master/tests/Resources/Magic/ClassForDispatcherTesting.php).

Features, considered for the future, include:

* preventing dynamic/undefined properties,

* serialization,

* working on evolving data structures without updating all clients (e.g. for new versions of domain events),

* validation?

* virtual setters? (Easy; but don't you want to write intention-revealing methods to transform the state of your objects?)

* read-only properties? (I'm comfortable with default getters.)


Checkout [src/Traits/] to find out, how it works. (I'm not sure if I want to deliver these traits in the future. It might be better to provide them as examples. So it might be a good idea to copy these traits in your project; or at least create your project-specific traits that include these traits.)


This library is based on [scaleupstack/metadata] (including [scaleupstack/annotations]), and [scaleupstack/reflection]. Via extension points in this library, and in [scaleupstack/metadata] you can add additional meta-programming for your use-cases. 

TODO: TBD


## Current State

This library will be developed further in the context of an internal project. I do not expect big refactorings or BC breaks. (One minor BC issue might be the removal of `src/Traits/` as mentioned above.)

If you are missing anything, feel free to contact me, or create a pull request.

Please, feel free to contact me, when you evaluate this library. I'd be happy to discuss ideas, or will be more sensible when breaking things.



## Contribute

Thanks that you want to contribute to ScaleUpStack/EasyObject.

* Report any bugs or issues on the [issue tracker].

* Get the source code from the [Git repository].


## License

Please check [LICENSE.md] in the root dir of this package.


## Copyright

ScaleUpVentures Gmbh, Germany<br>
Thomas Nunninger <thomas.nunninger@scaleupventures.com><br>
[www.scaleupventures.com]


[scaleupstack/metadata]: https://github.com/scaleupstack/metadata
[scaleupstack/reflection]: https://github.com/scaleupstack/reflection
[Composer]: https://getcomposer.org
[issue tracker]: https://github.com/scaleupstack/easy-object/issues
[Git repository]: https://github.com/scaleupstack/easy-object
[LICENSE.md]: LICENSE.md
[www.scaleupventures.com]: https://www.scaleupventures.com/
