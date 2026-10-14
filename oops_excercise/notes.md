
# Notes


> `ucfirst()` - makes the first letter uppercase.

> `preg_match()` - to control which characters are allowed.
``` bash
    if (!preg_match("/^[a-zA-Z ]+$/", $var_name))

```
>`is_numeric()` - to check whether the input is a number.


| Input | Validation |
|-------|------------|
|letters/space | `preg_match()`|
|Numbers | `is_numeric()` |
|Not empty / space  | `trim` |
| To split the string | `str_split()`|
| To shuffle  | `shuffle()` |
| To rejoin the letter to string | `implode()` |


### JSON = JavaScript Object Notation 

> It's a simple format used to store and exchange data. Like a text file that stores structured information.
----
> JSON uses key-value pairs 
---
``` bash 
  {
    "key" : "value",
    "name" : "Bob"
  }
```
---
> JSON -> PHP     `json_decode()`
---
> PHP  -> JSON    `json_encode()`
----
> JSON_PRETTY_PRINT   :  format the JSON with indendation and line breaks.
----
##### JSON = How the data is stored.

##### OOP = how your program organizes and works with that data


### Magic Square

> `requie_once` - used to load those files 

>`fgets(STDIN)` - reads what the user types

>`trim()`  - removes the unnecessary spaces and the newline.

#### Abstract CLass

An abstract class is a class that is meant to be used as a base class.
Cannot directly create its object.
It's used when classes have some common functionality/data.

#### Abstract Method

An abstract method has no implementation in the parent.
The child must implement it.

#### Dependency/ Passing Objects
Objects can work together by passing objects to methods.
#### Composition
Composition means one object contains or uses another object.  
*__Composition is havinng HAS-A relationship.__*  
***Inheritance is having a IS-A relationship.***

#### final and readonly

***final - prevents inheritance or overriding.***  
***readonly - means a property can be assigned but cannot normally be chnaged afterward.***  
***Traits - A trait allows you to reuse methods between unrelated classes. Reusable piece of class functionality.***
***Namespace - It help organize classes and avoid name conflicts.***  

### SOLID

*S - Single Responsibility :- One class should have one main responsibility*  
*O - Open/Closed :- Classes should be open for extension but closed for unnecessary modification.*  
*L - Liskov Substitution :- A child class should be usable wherever its parent is expected.*
*I - Interface Segregation :- Don't force a class to implement methods it doesn't need.*  
*D - Dependency Inversion :- High level classes should depend on abstractions rather than tightly depending on concrete implementations.*  
