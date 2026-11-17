$(document).ready(function() 
{
	if(marca_ativa>0 || familia_ativa>0 || produto_ativo>0){}
	else
	{	
		destroy_all_coockies();
	}
});

function destroy_all_coockies()
{
	var myCookies = getCookies();
	for(var a=0; a<myCookies.length;a++)
	{	
		if(substr_count_v(myCookies[a], 'c_product_sort')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_product_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_marca_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_destaque_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_novidade_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_promocao_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_category_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_preco_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_fam_id1_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_fam_id2_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_color_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_pagination_order')) $.cookies.set(myCookies[a], null);
	}
} 
var getCookies = function()
{
  var pairs = document.cookie.split(";");
  var cookies = new Array();
  for (var i=0; i<pairs.length; i++)
  {
    var pair = pairs[i].split("=");
    cookies[i] = pair[0];
  }
  return cookies;
}
function substr_count_v(haystack, needle, offset, length) 
{
    var pos = 0,
    cnt = 0; 
    haystack += '';
    needle += '';
    if (isNaN(offset)) 
	{
        offset = 0;    
	}
    if (isNaN(length)) 
	{
        length = 0;
    }
    offset--; 
    while ((offset = haystack.indexOf(needle, offset + 1)) != -1) 
	{
        if (length > 0 && (offset + needle.length) > length) 
		{
            return false;
        } 
		else 
		{            
			cnt++;
        }
    } 
    return cnt;
} 

function substr_count(haystack, needle, offset, length) 
{
    var pos = 0,
    cnt = 0; 
    haystack += '';
    needle += '';
    if (isNaN(offset)) 
	{
        offset = 0;    
	}
    if (isNaN(length)) 
	{
        length = 0;
    }
    offset--; 
    while ((offset = haystack.indexOf(needle, offset + 1)) != -1) 
	{
        if (length > 0 && (offset + needle.length) > length) 
		{
            return false;
        } 
		else 
		{            
			cnt++;
        }
    } 
    return cnt;
} 
