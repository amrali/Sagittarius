Sagittarius
===========

Sagittarius is an obfuscater that helps you compress and encode your code in BASE64
and if you so desire, encrypt it with Rabbit stream cipher(eSTREAM finalist).

Not just that, it does all of the above - if ordered - inside a self-contained script.

Requirements
------------

* PHP >= 5.3.0

Yes that's it, just a recent version of PHP!

Usage
-----

In the root directory you will find `example.php` and `code_sample.php`. Both files
are used to demonstrate the core functionality of Sagittarius.

However, I've put modularity in mind so that if you found some useful part you want
to utilize outside the context of Sagittarius, you would feel encouraged to do so.

To run the example, type in your favorite terminal:

    php example.php

Classes
-------

### Rabbit

The `Rabbit` class' constructor takes a single argument of the key to be used in the encryption
or decryption process. Key length must be exactly 16 bytes.

Function `encrypt` takes a single by-reference argument which is the data string to be encrypted.

Function `decrypt` takes a single by-reference argument which is the data string to be decrypted.

Function `reset` takes no arguments and its sole purpose is to reset the internal state of the algorithm
so if you did instantiate an object and did call `encrypt` you need to call `reset` before you can call `decrypt`.

`Rabbit` only have one property; `key` stores the key passed through the constructor as long as the object lives.


### Obfuscate

The `Obfuscate` class' constructor takes a single optional argument of the compression level to be used. Default[5]

Function `encode` takes two arguments, the first argument is required simply because it is the input to be encoded,
the second argument is optional and it takes a reference to a `Rabbit` instance. In the case the second argument
wasn't provided, encoding will not utilize encryption.

Function `decode` does the exact opposite of `encode`, and it does have the same arguments as well.

Function `encode_contained` takes the same arguments as `encode` and `decode` however, this function is used to
provide a self-containing script which you can use as standalone. Hence `input` here must be actual PHP code without
any PHP tags.

Copyright
---------

See the COPYRIGHT file.
