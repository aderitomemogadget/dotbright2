<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
// funcao que serve para substituir as sessoes
function set_cookie($variavel)
{
	global $$variavel;
	if(isset($_COOKIE["miscode_".$variavel]))
	{
		$past=time() - (3600*12);
		setcookie("memogadget_".$variavel, date("F jS - g:i a"), $past); // To remove
		setcookie("memogadget_".$variavel, $$variavel, time()+(3600*12), "/"); // 12 hora
	}
	else
	{
		setcookie("memogadget_".$variavel, $$variavel, time()+(3600*12), "/"); // 12 hora
	}
}
function get_cookie($variavel)
{
	global $$variavel;
	if(isset($_COOKIE["memogadget_".$variavel]))
	{
		$$variavel=$_COOKIE["memogadget_".$variavel];
	}
	else
	{
		$$variavel=$_cookie["memogadget_".$variavel];
	}
}
function delete_cookie()
{
    if(is_array($_COOKIE)){
        foreach ($_COOKIE as $key=>$value){
            if(strstr($key, "memogadget_")){
                setcookie($key, "", time() - (3600*12), "/");
            }
        }
    }
}