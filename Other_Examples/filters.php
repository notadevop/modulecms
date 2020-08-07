<?php


	//PHP Validations Filters

	FILTER_VALIDATE_BOOLEAN		//Checks for a valid Boolean value
	FILTER_VALIDATE_EMAIL		//Checks for a valid email address
	FILTER_VALIDATE_FLOAT		//Checks for a valid float value
	FILTER_VALIDATE_INT			//Checks for a valid integer value
	FILTER_VALIDATE_IP			//Checks for a valid IP address value
	FILTER_VALIDATE_REGEXP		//Checks for a valid regular expression value
	FILTER_VALIDATE_URL			//Checks for a valid URL string

	//PHP Sanitation Filters 

	FILTER_SANITIZE_EMAIL				//Removes illegal characters from an email address
	FILTER_SANITIZE_ENCODED				//Encodes special characters in the string
	FILTER_SANITIZE_MAGIC_QUOTES 		//Apply the addslashes() function
	FILTER_SANITIZE_NUMBER_FLOAT 		//Remove all characters, except digits, +, –, and E
	FILTER_SANITIZE_NUMBER_INT			//Removes all characters except digits and + or –
	FILTER_SANITIZE_SPECIAL_CHARS		//Removes any special characters in the string
	FILTER_SANITIZE_FULL_SPECIAL_CHARS	//Same as htmlspecialchars()
	FILTER_SANITIZE_STRING				//Removes HTML tags and special characters from a string
	FILTER_SANITIZE_STRIPPED			//Same as FILTER_SANITIZE_STRING
	FILTER_SANITIZE_URL					//Removes all illegal characters from a URL string
	
	//The PHP Filter Functions

	filter_has_var()		//Checks if a variable of the specified type exists
	filter_id()				//Returns the filter ID of the specified filter
	filter_input()			//Retrieves a value passed by GET, POST, sessions, or cookies and filters it
	filter_input_array()	//Retrieves multiple values passed to the PHP program and filters them
	filter_list()			//Returns a list of supported filters
	filter_var()			//Filters a variable
	filter_var_array()		//Filters a list of variables