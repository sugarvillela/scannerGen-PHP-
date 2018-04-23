<?php
//<?php
///**
// *  This program is free software; you can redistribute it and/or modify
// *  it under the terms of the GNU General Public License as published by
// *  the Free Software Foundation; either version 2 of the License, or
// *  (at your option) any later version.
// *
// *  This program is distributed in the hope that it will be useful,
// *  but WITHOUT ANY WARRANTY; without even the implied warranty of
// *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// *  GNU Library General Public License for more details.
// *
// *  You should have received a copy of the GNU General Public License
// *  along with this program; if not, write to the Free Software
// *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
// */
//
////This is donor code...
//function fileOpen( $f ){
//    if( !$f ){ return false; }
//    $t=get_resource_type( $f );
//    return ( $t=='stream' OR $t=='file' );
//}
//
//class scanEngine{
//    public $patterns, $actions, $text, $leng, $lval, $val, $in, $out;
//    protected $bestPatternId, $startIndex, $getter, $currText, $return;
//
//    function __construct( $inFileName='' ){
//        /* Arrays populated per Lex file */
//        $this->patterns=array();    //array of regexes
//        $this->actions=array();     //array of code from rules section
//        $this->bestPatternId='';    //Key of the pattern array element
//        /* Set up input */
//        $this->getter=null;         //Choose GET/POST or file input
//        $this->httpMethod='get';
//        $this->formField='ui';
//        /* String housekeeping */
//        $this->currText='';         //a line of input
//        $this->startIndex=0;        //Keep track of what's been 'eaten'
//        /* Below: These are found in flex as yytext, yyleng etc 
//         * Since this is OOP, we access them as $yy->text etc */
//        $this->text='';
//        $this->leng=0;
//        $this->in=null;
//        $this->out=null;
//        /* Unassigned */
//        $this->lval=-1;     
//        $this->val=-1;        
//    }
//    function __destruct() {
//        if( $this->in ){ fclose( $this->in ); }
//    }
//    function lex(){
//        if( !$this->getter ){
//            /* This only happens on first call to this function:
//             * Set input using a strategy pattern: if $this->in is an open
//             * file handle, instantiate the file object
//             * Otherwise instantiate a dummy object that returns one line
//             */
//            if( fileOpen( $this->in ) ){
//                $this->getter=new GetterF( $this->in );
//            }
//            else{
//                $text = ( $this->httpMethod=='get' )? 
//                    $_GET[$this->formField] : 
//                    $_GET[$this->formField];
//                $this->getter=new GetterT( trim( $text ) );
//            }
//            $this->currText=$this->getter->next();//initial line to read
//        }
//        /* Outer loop iterates file, or runs once in the case of form input */
//        while( $this->currText ){
//            /* Inner loop 'eats' current line left to right */
//            while( $this->startIndex<strlen( $this->currText ) AND self::setMatch() ){
//                /* Once best pattern is chosen, send to the switch statement */
//                $returnMe=actionMap( $this->bestPatternId );
//                /* If rule action returns, $this->return is true.
//                 * Otherwise $returnMe is not used */
//                if( $this->return ){
//                    return $returnMe;
//                }
//            }
//            $this->startIndex=0; 
//            $this->currText=$this->getter->next();
//        }
//        return 0;
//    }
//    function setMatch(){
//        $this->text='';
//        $this->leng=0;
//        $this->bestPatternId='';
//        $found=false;
//        $matches;
//        /* Make temp string: everything not yet eaten */
//        $s=substr( $this->currText, $this->startIndex );
//        foreach ( $this->patterns as $id => $pattern ) {
//            /* ^ anchors match to the left */
//            if( preg_match( '/^'.$pattern.'/', $s, $matches ) ){
//                $len=strlen( $matches[0] );
//                if( $len>$this->leng ){
//                    $this->text=$matches[0];
//                    $this->leng=$len;
//                    $this->bestPatternId=$id;
//                }
//                $found=true;
//                break;//prioritizes first match on equal-size substrings
//            }
//        }
//        $this->startIndex+=$this->leng; 
//        return $found;
//    }
//    function setHTTPMethod( $get_or_post ){
//        $this->httpMethod=$get_or_post;
//    }
//    function setFormField( $fieldName ){
//        $this->formField=$fieldName;
//    }
//    function actionReturned( $set=true ){
//        $this->return=$set;
//    }
//    /* Below: Lex functions not implemented */
//    function wrap(){}
//    function more(){}
//    function less( $k ){}
//
//}
//abstract class Getter{
//    /* Common interface for file or text input 
//     * GetterF next() wraps fgets()
//     * GetterS next() returns false after one call
//     */
//    abstract function next();
//}
//class GetterF extends Getter{
//    protected $in;
//    function __construct( $fileHandle ) {
//        $this->in=$fileHandle;
//    }
//    function __destruct() {
//        if(fileOpen( $this->in ) ){ fclose( $this->in ); }
//    }
//    function next(){
//        return( feof( $this->in ) )? '' : fgets( $this->in );
//    }
//}
//class GetterS extends Getter{
//    protected $text, $done;
//    function __construct( $setText ) {
//        $this->text=$setText;
//        $this->done=0;
//    }
//    function next(){
//        return( 0<$this->done++ )? '' : $this->text;
//    }
//}
//$yy=new scanEngine();

//STOP//
/* ^^ Don't delete the line above */


function dispArray($array){
    /* For dev...prettier than var_dump */
    foreach ($array as $key => $value) {
        echo "$key: $value<br>";
    }
    echo "<br>";
}
/* General functions called by this page */
function visibleChar( $ch ){//false on space, tab, newLine etc
    return ord( $ch )>32;
}
function trimTail( $in ){//Keep whitespace at string beginning, trim end
    $out='';
    for ($i = 0; $i < strlen( $in ); $i++) {
        if( visibleChar( $in[$i] ) ){
            break;
        }
        $out.=$in[$i];
    }
    return $out.trim( substr( $in, strlen( $out ) ) );
}
function splitOnTab( $in ){//for tab or space separated, limit 2
    $out=array('','');
    $write=false;
    $dest=0;
    for ($i = 0; $i < strlen( $in ); $i++) {
        if( $write ){
            if( !visibleChar( $in[$i] ) ){
                $dest=1;
            }
            $out[$dest].=$in[$i];
        }
        else if( visibleChar( $in[$i] ) ){
            $write=true;
            $out[$dest].=$in[$i];
        }
    }
    return array( trim( $out[0] ), trim( $out[1] ) ); 
}
function wrappedBy( $in, $first, $last='' ){
    /* Return true on (text) or "text" Whatever the first or last char is */
    if( !$last ){
        $last=$first;
    }
    return ($in[0]==$first AND $in[strlen( $in )-1]==$last );
}
function qLitToRegex( $text ){
    /* If text is quoted, remove and return regex-sanitized */
    return ( wrappedBy( $text, '"') )?
            str_replace( '/', '\/', preg_quote ( substr( $text, 1, -1 ) ) ) : 
            $text;
}
/* Enumerations for scannerGen states relating to Lex format */
abstract class State{
    const copy1 =   0;  //Area between %{ and %} Copies verbatim
    const def =     1;  //Area for defining patterns. See setDef below
    const rule =    2;  //Area between %% markers. See setRule below
    const copy2 =   3;  //Area after last %%  Copies verbatim
}
class scannerGen{
    private $fin, $fout, $donor,    //file handles
            $textIn,    
            $state,                 //holds above enumerated state
            $defPatterns, $rulePatterns, $rules, //arrays
            $globList,              //array holds user-defined vars: see setDef
            $lineNumber, $err;      //to display errors with line numbers
    
    public function __construct( $fnameRead, $fnameWrite ) {
        $this->fin = fopen( $fnameRead,"r");
        $this->fout = fopen( $fnameWrite,"w");
        /* The donor code is the commented text at the top of this file */
        $this->donor = fopen( "commons/scannerGen.php","r");
        $this->textIn='';
        $this->state=State::def;        //make initial %{ %} optional
        $this->defPatterns=array();     //Regexes or literals
        $this->rulePatterns=array();     //Regexes or literals
        $this->rules=array();         //user-defined code run on pattern match
        $this->globList=array( '$yy' ); //see setDef
        $this->lineNumber=0;
        $this->err=array();//can hold multiple errors. See 'go' function below
    }
    public function __destruct() {
        if( $this->fin ){ fclose( $this->fin ); }
        if( $this->fout ){ fclose( $this->fout ); }
    }
    private function changeState(){
        /* Responds to symbols in the Lex file
         * Some important things happen on the transition from state::rule to
         * state::copy2. writePatterns and writeActionMap dump arrays to 
         * destination file */
        switch ( $this->textIn ){
            case '%{':
                echo $this->textIn.': new state<br>';
                $this->state=State::copy1;
                return true;
            case '%}':
                echo $this->textIn.': new state<br>';
                $this->state=State::def;
                return true;
            case '%%':
                echo $this->textIn.': new state<br>';
                if( $this->state == State::def ){
                    $this->state=State::rule;
                }
                else{
                    self::writePatterns();
                    self::writeActionMap();
                    $this->state=State::copy2;
                }
                return true;
            default:
                return false;
        }
    }
    public function go(){
        /* This is the public interface of the scanner generator 
         * Processes the Lex file line-by-line. */
        if( !$this->fin OR !$this->fout OR !$this->donor ){
            self::setErr( 'File Error' );
            return;
        }
        /* Init: copy scanEngine and related code to top of destination file */
        self::copyDonorSegment();
        /* Emulate state machine: different procedure for  each state */
        while( !feof( $this->fin ) ){
            /* Get a line from file: save trimmed and not trimmed */
            $textInRaw = fgets( $this->fin );
            $this->textIn = trim( $textInRaw );
            $this->lineNumber++;
            /* skip blank line or %% symbol */
            if( 
                !strlen( $this->textIn ) OR 
                self::changeState() 
            ){
                continue;
            }
            /* These are all string operations.
             * setDef and setRule accumulate to arrays. 
             * Dumps happen on state transitions, called from changeState()
             */
            switch ( $this->state ){
                case State::copy1:
                case State::copy2:
                    /* Copy, preserving tabs */
                    fputs( $this->fout, trimTail( $textInRaw ).PHP_EOL );
                    break;
                case State::def:
                    self::setDef();
                    break;
                case State::rule:
                    self::setRule();
                    break;
                default:
                    break;
            }
            /* Quits on error. Delete this block to process through and 
             * see all errors */
            if( self::isErr() ){
                break;
            }
        }
//        echo "defPatterns<br>";
//        dispArray( $this->defPatterns );
//        echo "rulePatterns<br>";
//        dispArray( $this->rulePatterns );
//        echo "Actions<br>";
//        dispArray( $this->rules );
        
        fputs( $this->fout, '?>'.PHP_EOL );
        self::dispErr();
    }
    /* Initialize destination file with donor code */
    function copyDonorSegment(){
        /* Copies scanEngine from donor file to top of destination file.
         * Stops at //STOP// marker */
        $line=fgets( $this->donor );//discard first line
        while( !feof( $this->fin ) ){
            $line=fgets( $this->donor );
            if( trim( $line ) == '//STOP//' ){
                fputs( $this->fout, PHP_EOL );//just a space
                break;
            }
            /* Uncomment the code and copy to destination */
            fputs( $this->fout, substr( $line, 2 ) );
        }
    }
    function setDef(){
        /* This section of lex file is formatted one of three ways: 
         * identifier   regex OR
         * identifier   "pattern" OR
         * %global      $var1 $var2
         * --regex must work in PHP's Perl-compatible engine OR
         * --Pattern must have quotes OR
         * --%global must list all global variables defined in the %{ %} section
         * This is necessary for user-supplied rules to work: PHP requires
         * global variables be re-declared inside any function they are used.
         * Since scannerGen copies %{ %} section verbatim, it does not know 
         * what those variables are
         */
        $keyVal=splitOnTab( $this->textIn );
        /* Assert 2-element array for LHS RHS */
        if( count( $keyVal )!= 2 ){
            self::setError('Improper Definition');
            return;
        }
        /* Case: line contains %global list */
        if( strpos( $keyVal[0], '%glob' )!==false ){
            /* Handle comma-separated list or space-separated */
            $keyVal[1]=str_replace( ',', ' ', $keyVal[1] );
            $list=explode( ' ', $keyVal[1] );
            foreach ( $list as $globVar ) {
                $globVar=trim( $globVar );
                if( strlen( $globVar ) ){
                    $this->globList[]=$globVar;
                }
            }
            return;
        }
        $keyVal[1]=qLitToRegex( $keyVal[1] );
        if( strpos( $keyVal[1], '{')!==false ){
            /* replace {int} with previously defined [0-9]+*/
            foreach( $this->defPatterns as $scanKey => $scanVal ) {
                $keyVal[1] = str_replace( 
                    '{'.$scanKey.'}', 
                    $scanVal, 
                    $keyVal[1]
               );
            }
        }
        $this->defPatterns[$keyVal[0]]=$keyVal[1];//non-unique var names overwrite
    }
    function setRule(){
        /* This section of lex file is formatted one of two ways: 
         * identifier   { php code for action } OR
         * "pattern"    { php code for action }
         * --Identifier must be defined in prev section
         * --Pattern must have quotes
         * --Action must be surrounded by curly braces
         */
        $keyVal=splitOnTab( $this->textIn );
        /* Assert 2-element array for LHS RHS */
        if( count( $keyVal )!= 2 ){
            self::setError( 'Improper Definition' );
            return;
        }
        /* Assert RHS surrounded by curly braces */
        if( wrappedBy( $keyVal[1], '{', '}' ) ){
            //remove the braces
            $keyVal[1]=trim( substr( $keyVal[1], 1, -1) );
        }
        else{
            self::setErr( 'Right-hand-side improperly defined' );
            return;
        }
        
        /* Case: LHS is quoted pattern */
        if( wrappedBy( $keyVal[0], '"' ) ){
            /* Make a version with just the quotes stripped for identifier
             * Make another version with escape characters for regex */
            $stripped=substr( $keyVal[0], 1, -1);
            $escaped=qLitToRegex( $keyVal[0] );
            $this->rulePatterns[$stripped]=$escaped;//
            $this->rules[$stripped]=$keyVal[1];
        }
        /* Case: LHS is previously defined identifier */
        else if( 
            isset( $this->defPatterns[$keyVal[0]] ) OR 
            isset( $this->rulePatterns[$keyVal[0]] ) 
        ){
            $this->rules[$keyVal[0]]=$keyVal[1];
        }
        else{
            self::setErr( 'Left-hand-side improperly defined' );
            return;
        }
    }
    function writePatterns(){
        /* There are two sections where patterns are defined: the definition
         * section where regexes and quoted literals are written, and the rule
         * section where more quoted literals can be written.  The unknown 
         * symbol regex, a dot, needs to be last on the list, otherwise it
         * grabs matches from other single-character patterns.  Thus this 
         * function writes patterns from the rule section before the patterns
         * from the definition section.
         */
        foreach ( $this->rulePatterns as $id => $pattern ) {
            fputs( 
                $this->fout, 
                PHP_EOL.'$yy->patterns['."'".$id."']=".
                "'".$pattern."';".PHP_EOL );
        }
        foreach ( $this->defPatterns as $id => $pattern ) {
            fputs( 
                $this->fout, 
                PHP_EOL.'$yy->patterns['."'".$id."']=".
                "'".$pattern."';".PHP_EOL );
        }
    }
    function writeActionMap(){
        /* This writes the function that carries out the rule actions. 
         * Start by making a string to declare global vars inside function
         */
        $globDec='';
        foreach ( $this->globList as $globVar ) {
            $globDec.="global $globVar; ";
        }
        /* Write the actionMap function:
         * It's a switch statement with actions by the pattern array key 
         * What is $yy->actionReturned?
         * $yy needs to know if the function returned. That is determined by
         * the user code in the rule section. So, actionMap() calls
         * $yy->actionReturned() to set it to the default of true.  
         * $yy->actionReturned() called again after the switch statement 
         * completes, setting it to false.
         */
        fputs( 
            $this->fout, 
            PHP_EOL.'function actionMap( $action ){'.PHP_EOL.
            '   '.$globDec.PHP_EOL.
            '   $yy->actionReturned();'.PHP_EOL.//See above
            '   switch ( $action ){'.PHP_EOL
        );
        foreach ( $this->rules as $id => $action ) {
            fputs( 
                $this->fout, 
                "       case '".$id."':".PHP_EOL.
                '           '.$action.PHP_EOL.
                '           break;'.PHP_EOL
            );
        }
        fputs( 
            $this->fout, 
            '       default:'.PHP_EOL.
            '       break;'.PHP_EOL.
            '   }'.PHP_EOL.
            '   $yy->actionReturned( false );'.PHP_EOL.//See above
            '   return true;'.PHP_EOL.
            '}'.PHP_EOL
        );
    }

    function setErr( $desc ){
        $this->err[]="Error line $this->lineNumber: $desc: $this->textIn";
    }
    function dispErr(){
        foreach ( $this->err as $value) {
            echo "$value<br>";
        }
    }
    function isErr(){
        return count( $this->err )>0;
    }
}
?>
