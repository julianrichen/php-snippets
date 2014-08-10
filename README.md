# snippets
A collection of (mostly) PHP snippets intended for users of PHPAcademy.org / CodeCourse.com

# What do these snippets offer?
These snippets are pre-built scripts that full fill a specific need, however, more importantly they offer a detail comments and explanation to help new coders understand what the snippet is doing.

# Navigating snippets
Snippets are stored in the appropriate language directory, for example a PHP file would be stored in the directory "PHP" in the root. Inside of each language directory are categories, for example if I wanted to look for a snippet that modified or gave me info about time I would look under "DateTime". If I wanted a snippet that dealt with downloading files I would look in "Downloads" directory.

Inside of each category are files containing snippets. Files that start with a lower case and use camel case are functions, these files may have one or more functions in them. Files that start with an uppercase and camel case are classes'.

# Using a snippet
Download the snippet from the git and include it in your script, each snippet contains a detailed intro comment that contains various information which includes.
* The purpose of the snippet / what it does
* Version info
* Author, author's site, git and license
* Requirements for using the snippet (PHP version, extensions, etc..)
* Suggestions that might make it easier to use the snippet
* Usage section / way to implement and call snippet

After the main comment you will also find another comment block for each function/method in the snippet that explains its purpose, parameters it takes and what it returns. Read them if you are lost or need help.

# Can I contribute
Absolutely! When you create a new snippet make sure you follow these guidelines:

1. Select the appropriate template file from /root/Templates/<file language>/template.ext
    * If one does not exists create one using guidelines from the other templates, if it is acceptable it will be added
2. Fill in all the information in the first comment block in the template
3. Provided code that creates the desired effect as efficiently as possible with minimal code & system resource (**Do your research**)
4. Please do not use shorthand versions of code & class/function/method/variable names that are ambiguous or don't explain their purpose
5. Try and use the PEAR coding standard, however, slight variations are acceptable to a degree.
6. Each part of the code is commented to explain its action or the reason it was included
    * These snippets might be usable out of the box but they are intended to teach concepts