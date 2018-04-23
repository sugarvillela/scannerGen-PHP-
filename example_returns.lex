%{

$pos=0; $line=0;

function stop( $errMessage ){
    global $yy;
    global $line;
    global $pos;
    printf("%s:  line %d, column %d, text '%s'<br>", $errMessage, $line, $pos, $yy->text);
}

function go( $desc ){
    global $yy;
    global $pos;
    printf( "%s<br>", $desc );
    $pos+=$yy->leng;
}

function goLiteral( $desc ){
    global $yy;
    global $pos;
    $out=$desc.' '.$yy->text;
    $pos+=$yy->leng;
    printf( "%s<br>", $out );
}

%}

%global         $pos, $line

varName		[a-zA-Z](([a-zA-Z]|[0-9]|[_])*([a-zA-Z]|[0-9]))*
lit_float	[0-9]*\.[0-9]+
lit_int		[0-9]+

space           [\s\t]+
newline         "\n"
comment         ##(.)*
wild		.

%%

"+"		{return "PLUS";}
"-"             {return "MINUS";}
"*"		{return "MULT";}
"/"             {return "DIV";}
"="             {return "EQUAL";}

varName		{return "VARNAME $yy->text";}
lit_float	{return "FLOAT $yy->text";}
lit_int		{return "NUMBER $yy->text";}
space		{$pos += $yy->leng;}
newline		{$pos = 1; $line++;}
comment		{$pos = 1; $line++;}
wild		{stop( "Unknown symbol" ); }

%%

function main( $inFileName='', $outFileName='' ){
    global $yy;
    if( $inFileName ){
        $yy->in=fopen( $inFileName, 'r' );
    }
    $result;
    while( ( $result=$yy->lex() ) ){
        echo "$result<br>";
    }
}
