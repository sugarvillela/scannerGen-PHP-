# scannerGen
## Lex-Format Scanner Generator for PHP
This program generates a PHP-coded scanner from a Lex-Formatted file.
## Inspiration
> Leveraging the power of the regex engine in any framework, you can build a scanner generator with just a few pages of code.  The generator is a four-state machine that copies and manipulates strings.  The scanner itself is little more than a max function.
## About Flex
* If not familiar, obviously start here:  https://en.wikipedia.org/wiki/Flex_(lexical_analyser_generator)
* Lex file structure:  Just so we agree about the names of the sections:
>* Section One: **Code**
>> * Symbol %{
>>>  * Your code to be copied to new file
>> * Symbol %}
>* Section Two: **Definitions**
>>>  * identifier	(tab) regex or quoted literal
>* Section Three: **Rules**
>> * Symbol %%
>>>  * identifier	(tab) your code in curly braces
>> * Symbol %% 
>* Section Four: **Code**
>>>  * More of your  code to be copied to new file
## scannerGen notes:
* If you are defining global variables in your code section...  
> ...you need to list them in the definition section.  Use the keyword **%global** followed by a tab then a space- or comma- separated list. (See example Lex file in repository.)  This is to satisfy the PHP requirement that global variables be re-declared inside functions in which they are used.
* PHP regular expressions are not the same as Flex regular expressions.
> In Flex regex, double quotes can be found in the regular expression to indicate literal text.  PHP regexes don't have quotes around literal text.  In fact, don't put quotes in regexes at all.
* Interpretation of quotes in **scannerGen**:
>Quoted text is sanitized for PHP regex engine, meaning something like "+" will be turned into this "\\+".  That way the literal plus sign is interpreted instead of the regex operator.  Give literal text its own pattern; don't mix and match with regexes.
* Placement of patterns: Definition section or Rule Section?
>* Put regexes in the Definition section only
>* Quoted literals can be in Definition section on the right hand side and/or in the Rule section on the left hand side
* Placement of Dot operator:
> The dot operator is handy for finding unmatchable text.  It's a wild card regex.  Don't put it in quotes because quoted text has special characters escaped...**scannerGen** will think it's just a dot.  Instead, define it in the Definition section.  Put it last, so it doesn't grab legitimate text.
* Priority of equal-length matches:
> Where two patterns of equal length are matched, the one that appears first is the winner.  This happens most often with single character patterns.  To keep the dot operator last, the patterns from the Rule section are written before patterns from the Definition section. (See code comments)
* Input Select follows Lex example:
>* This means the default is the computer keyboard.  Or you can open a read file and assign the handle to $yy->in (see example in repository).  The generated scanner  will scan the whole file.
>* For keyboard input, the scanner defaults to $_GET['ui'], where $_GET is form data and 'ui' is an arbitrary field name.  You can choose $_POST using the setHTTPMethod() function, passing 'post'.  You can change the field name using the setFormField() function.
* Known Issues (besides any of the above)
>* I left some things unfinished. **scannerGen** doesn't assign anything to standard Lex variables yyval and yylval
>* I didn't add comment support.  You can comment your Copy section code all you want, just not the Definition and Rule section.
