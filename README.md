# refactoring


> as we talked yesterday i started working on the task around 12:30 and completed it at 1:45 as it was instructed that invest no more than two hours;

> tried my best to refactoring the code :)

> removed unnecessary elses
> removed unnecessary variables
> removed duplicated variable with same values


>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

As every developer have their style of writing code but the code that was given to me for refactoring that code seriously needed some attention. 

following are the problems that i have seen in that code:

>dupication of variables: there was many varaibles that were intialized, each variables doing same thing. more varaibles means more complexity.

> that lead to the second issue that i have seen nameing conventions of the variables: as there was many variables and doing exacat same but stantdard were not following for nameing variable some variable named User and on the sametime exact same variable was named something else although they were doing the same thing.

> unnecessary if else: logic that should not take more than one line was written in 4 to 5 lines. 

> unnecessary data fetching: there was this issue that i saw that there was need of some data to be fetched depended upon user type.  so ,there was this if else contion that if the userid exist fetch data in the varaible and in the else if condition it checks the user type then fetch data in the same variable everytime first it checks user id exist fetch data and then check if that user is admin than rewrite that data variable. :D

dupication of code: there was alot of duplicated code :/


>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

things than i would have done:

> firstly i would have reduced the number of  variables and standardized it by creating global varialbes.

> would have implementd more  efficient logic to reduce number of lines and duplicated code by creating more function that will contain those process that are repeating more than one time.

> implement logic in such a maner than no extra process should take place.

>and i would have used less if else  :D
