<?php 

// Backtracert 


function backtrace()
{
    $bt = debug_backtrace();
    
    echo("<br /><br />Backtrace (most recent call last):<br /><br />\n");    
    for($i = 0; $i <= count($bt) - 1; $i++)
    {
        if(!isset($bt[$i]["file"]))
            echo("[PHP core called function]<br />");
        else
            echo("File: ".$bt[$i]["file"]."<br />");
        
        if(isset($bt[$i]["line"]))
            echo("line ".$bt[$i]["line"]."<br />");
        echo("function called: ".$bt[$i]["function"]);
        
        if(isset($bt[$i]["args"]))
        {
            echo("<br /> args: ");
            for($j = 0; $j <= count($bt[$i]["args"]) - 1; $j++)
            {
                if(is_array($bt[$i]["args"][$j]))
                {
                    print_r($bt[$i]["args"][$j]);
                }
                else
                    echo($bt[$i]["args"][$j]);    
                            
                if($j != count($bt[$i]["args"]) - 1)
                    echo(", ");
            }
        }
        echo("<br /><br />");
    }
}