# laravel-validation

Laravel validation setup that makes validation easier to use and to re-use. 

## Example

You wrap your validation logic in a class like the one below to clean op your controllers. 

### Validation class

```php

namespace App\Validation;

use Gnoesiboe\Validation\Validator;
use Gnoesiboe\Validation\ValidationRuleSet;
use Gnoesiboe\Validation\ValidationRuleSetInterface;

/**
 * Class CreateUserValidator
 */
class RegisterAccountValidator extends Validator
{

    /**
     * @return ValidationRuleSetInterface
     */
    protected function configureValidationRuleSet()
    {
        $stepOne = new ValidationRuleSet(array(
            'username'              => 'required|min:5|max:60',
            'password'              => 'required|min:5',
            'password_confirmation' => 'required',
            'email'                 => 'required|email|max:255'
        ));

        // add another validator set that only needs to be applied
        // after the first validation set passed

        $stepTwo = new ValidationRuleSet(array(
            'username' => 'unique:account,username',
            'password' => 'confirmed',
            'email'    => 'unique:account,email',
        ));
        $stepOne->chain($stepTwo);

        return $stepOne;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function normalizeInput(array $input)
    {
        $defaults = array(
            'username'              => null,
            'pasword'               => null,
            'password_confirmation' => null,
            'email'                 => null,
        );

        return array_merge($defaults, $input);
    }
}
```

Multiple ValidationRuleSet's can be applied to chain validation. The second rule set is only executed when the first passed as a whole. 

I make it a habit to normalize my input to make sure that all keys are availble and not leave it to the input that is put into the validator when the `validate(array $input)` is called. The normalized input is returned when the `validate` method is called. See below..

### Controller

In your controller action (or closure, or command, or whatever you prefer) use the validator like this:

```php

Route::get('/account/register', function () {
  $taintedInput = \Input::all();

  $validatedAndNormalizedInput = \App::make(RegisterAccountValidator::getClass())
    ->validate($taintedInput);
    
  // register the account
});
```

### Exceptions

When the defined rule sets for the validator class do not validate, an exception is thrown of type `ValidatorException`. You can catch this wherever you want and retrieve the message bag from it to add the the session for returning the validation messages. I like putting it in the global.php file like this:

_Example:_

```php

// in global.php..

App::error(function (\Gnoesiboe\Validation\ValidatorException $exception) {
    return Redirect::back()
        ->withInput($exception->getInput())
        ->withErrors($exception->getMessageBag());
});

```

## ValidationRuleSet

With a ValidationRuleSet you can pretty much do whatever you do with your `\Validator::make()`. Its just more like a builder and they can be chained like:

```php
<?php

$firstValidationRuleSet->chain($secondValidationRuleSet);

```
