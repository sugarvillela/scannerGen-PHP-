<?php
/**
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

//This is donor code...
function fileOpen( $f ){
    if( !$f ){ return false; }
    $t=get_resource_type( $f );
    return ( $t=='stream' OR $t=='file' );
}

class scanEngine{
    public $patterns, $actions, $text, $leng, $lval, $val, $in, $out;
    protected $bestPatternId, $startIndex, $getter, $currText, $return;

    function __construct( $inFileName='' ){
        /* Arrays populated per Lex file */
        $this->patterns=array();    //array of regexes
        $this->actions=array();     //array of code from rules section
        $this->bestPatternId='';    //Key of the pattern array element
        /* Set up input */
        $this->getter=null;         //Choose GET/POST or file input
        $this->httpMethod='get';
        $this->formField='ui';
        /* String housekeeping */
        $this->currText='';         //a line of input
        $this->startIndex=0;        //Keep track of what's been 'eaten'
        /* Below: These are found in flex as yytext, yyleng etc 
         * Since this is OOP, we access them as $yy->text etc */
        $this->text='';
        $this->leng=0;
        $this->in=null;
        $this->out=null;
        /* Unassigned */
        $this->lval=-1;     
        $this->val=-1;        
    }
    function __destruct() {
        if( $this->in ){ fclose( $this->in ); }
    }
    function lex(){
        if( !$this->getter ){
            /* This only happens on first call to this function:
             * Set input using a strategy pattern: if $this->in is an open
             * file handle, instantiate the file object
             * Otherwise instantiate a dummy object that returns one line
             */
            if( fileOpen( $this->in ) ){
                $this->getter=new GetterF( $this->in );
            }
            else{
                $text = ( $this->httpMethod=='get' )? 
                    $_GET[$this->formField] : 
                    $_GET[$this->formField];
                $this->getter=new GetterT( trim( $text ) );
            }
            $this->currText=$this->getter->next();//initial line to read
        }
        /* Outer loop iterates file, or runs once in the case of form input */
        while( $this->currText ){
            /* Inner loop 'eats' current line left to right */
            while( $this->startIndex<strlen( $this->currText ) AND self::setMatch() ){
                /* Once best pattern is chosen, send to the switch statement */
                $returnMe=actionMap( $this->bestPatternId );
                /* If rule action returns, $this->return is true.
                 * Otherwise $returnMe is not used */
                if( $this->return ){
                    return $returnMe;
                }
            }
            $this->startIndex=0; 
            $this->currText=$this->getter->next();
        }
        return 0;
    }
    function setMatch(){
        $this->text='';
        $this->leng=0;
        $this->bestPatternId='';
        $found=false;
        $matches;
        /* Make temp string: everything not yet eaten */
        $s=substr( $this->currText, $this->startIndex );
        foreach ( $this->patterns as $id => $pattern ) {
            /* ^ anchors match to the left */
            if( preg_match( '/^'.$pattern.'/', $s, $matches ) ){
                $len=strlen( $matches[0] );
                if( $len>$this->leng ){
                    $this->text=$matches[0];
                    $this->leng=$len;
                    $this->bestPatternId=$id;
                }
                $found=true;
                break;//prioritizes first match on equal-size substrings
            }
        }
        $this->startIndex+=$this->leng; 
        return $found;
    }
    function setHTTPMethod( $get_or_post ){
        $this->httpMethod=$get_or_post;
    }
    function setFormField( $fieldName ){
        $this->formField=$fieldName;
    }
    function actionReturned( $set=true ){
        $this->return=$set;
    }
    /* Below: Lex functions not implemented */
    function wrap(){}
    function more(){}
    function less( $k ){}

}
abstract class Getter{
    /* Common interface for file or text input 
     * GetterF next() wraps fgets()
     * GetterS next() returns false after one call
     */
    abstract function next();
}
class GetterF extends Getter{
    protected $in;
    function __construct( $fileHandle ) {
        $this->in=$fileHandle;
    }
    function __destruct() {
        if(fileOpen( $this->in ) ){ fclose( $this->in ); }
    }
    function next(){
        return( feof( $this->in ) )? '' : fgets( $this->in );
    }
}
class GetterS extends Getter{
    protected $text, $done;
    function __construct( $setText ) {
        $this->text=$setText;
        $this->done=0;
    }
    function next(){
        return( 0<$this->done++ )? '' : $this->text;
    }
}
$yy=new scanEngine();

function dispArray($array){
    foreach ($array as $key => $value) {
        echo "$key: $value<br>";
    }
}
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
//    if( $yyout && fputs( desc, yyout )<0 ){
//        stop( "Error writing to file" );
//    }
}
function goLiteral( $desc ){
    global $yy;
    global $pos;
    $out=$desc.' '.$yy->text;
    $pos+=$yy->leng;
    printf( "%s<br>", $out );
//    if( $yyout && fputs( desc, yyout )<0 ){
//        stop( "Error writing to file" );
//    }
}

$yy->patterns['+']='\+';

$yy->patterns['-']='\-';

$yy->patterns['*']='\*';

$yy->patterns['/']='\/';

$yy->patterns['=']='\=';

$yy->patterns['varName']='[a-zA-Z](([a-zA-Z]|[0-9]|[_])*([a-zA-Z]|[0-9]))*';

$yy->patterns['lit_float']='[0-9]*\.[0-9]+';

$yy->patterns['lit_int']='[0-9]+';

$yy->patterns['space']='[\s\t]+';

$yy->patterns['newline']='\\n';

$yy->patterns['comment']='##(.)*';

$yy->patterns['wild']='.';

function actionMap( $action ){
   global $yy; global $pos; global $line; 
   $yy->actionReturned();
   switch ( $action ){
       case '+':
           return "PLUS";
           break;
       case '-':
           return "MINUS";
           break;
       case '*':
           return "MULT";
           break;
       case '/':
           return "DIV";
           break;
       case '=':
           return "EQUAL";
           break;
       case 'varName':
           return "VARNAME $yy->text";
           break;
       case 'lit_float':
           return "FLOAT $yy->text";
           break;
       case 'lit_int':
           return "NUMBER $yy->text";
           break;
       case 'space':
           $pos += $yy->leng;
           break;
       case 'newline':
           $pos = 1; $line++;
           break;
       case 'comment':
           $pos = 1; $line++;
           break;
       case 'wild':
           stop( "Unknown symbol" );
           break;
       default:
       break;
   }
   $yy->actionReturned( false );
   return true;
}
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
?>
