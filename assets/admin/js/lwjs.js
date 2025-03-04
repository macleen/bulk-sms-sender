/*
******************************************************************************************
* lw: LightWeight JS Helpers 
*     containing a handy storage engine 
*     (local and cross session) using the Browser native (session and local storage API)
*     2 MB to 10 MB size of data can be stored on the client machine depending on the browser  
*     for chrome: Web storage APIs (LocalStorage & SessionStorage ) remain fixed at 5 MB.
*
* Licence: MIT / free to use and distribute
*
* Author: C. Mahmoud / MacLeen 2024 v 2.0.0 / email: acutclub@gmail.com
*
******************************************************************************************
*/
class LwJs {

    static _v = "LwJs v2.0.0 - macLeen 01-2024";
    static reserved = ['__engine_type__', ];
    static _available_storage_engines = {
        session:   window.sessionStorage,
        permanent: window.localStorage
    };


    constructor( ) {
        this.version = LwJs._v;
        this.available_storage_engines = Object.keys( LwJs._available_storage_engines );
    }

   //********************************* */    
 
    #composed( f, g ){
        return function ( ...p ) {
            return f( g( ...p ));
        }
    }    

    #piped( f, g ){
        return function( ...p ) {
            return g( f( ...p ));   
        }
     }    


    compose( ...fs ) {
        return fs.reduce( this.#composed );
    }

    pipe( ...fs ) {
        return fs.reduce( this.#piped );
    }

    //*************************************************************

    is_callable( f ) {
        return ['[object AsyncFunction]', '[object Function]'].includes( Object.prototype.toString.call( f )) || 
                "function" === typeof f || f instanceof Function;
    }

    //*************************************************************
    
    is_promise( p ) {    
        return this.type_of( p ) === 'promise' || this.is_callable( p.then );        
    }

    //*************************************************************
    
    clone( o ) { 
        let dest;
        Object.assign( dest, ...o );
        return dest;
    }

    /*************************************************************
    * usage: l().type_of( o ) 
    *       possible return values:
    *           - primitives ( number, string, boolean, null, undefined )
    *           - function
    *           - asyncfunction
    *           - generatorfunction
    *           - constructorfunction
    *           - promise
    *           - object
    *           - array
    * 
    *************************************************************
    */
    type_of( o ) {
        return [ undefined, 'undefined', null ].includes( o ) ? o 
                : new RegExp("\\[.+ (.+)\\]").exec({}.toString.call( o ).toLowerCase( ))[1];
    }
              
    //************************************************************* 

    storage_engine( engine_type ) {

        const self = this;
        if ( engine_type in LwJs._available_storage_engines ) {
             let selected_engine = LwJs._available_storage_engines[engine_type];

             return new Proxy({ }, { 

                 deleteProperty( _, k ) {
                         if ( LwJs.reserved.includes( k ))
                              throw new Error(`access denied, ${k} is a reserved word.`);
                         return k === 'all' ? selected_engine.clear( ) : selected_engine.removeItem( k );
                 },
                 get( _, k ) {
                         return ( k === '__engine_type__') ? engine_type
                              : ( selected_engine.getItem( k ) ?? 'undefined');
                 },
                 set( _, k, v ) {

                     if ( LwJs.reserved.includes( k ))
                          throw new Error(`access denied, ${k} is a reserved word.`);
                     if ( self.type_of( k ) != 'string' || self.type_of( v ) != 'string')
                          throw new Error(`Unsupported type. Both key and value must be strings.`);
                     return selected_engine.setItem( k, v );
                 },

             })
        } else throw new Error('Engine type is unavailable');
    }

    //*************************************************************

    random_number( maxlength ){            
        return Number(String(	Math.random( )).replace('0.','').substr(0, maxlength ?? 20) );
    }

    //*************************************************************

    random_segment_number( length ) {            
        return Math.floor(Math.random() * ( Math.pow( 10, length )));
    }

    //*************************************************************

    random_array_element( arr ) {
        arr = this.type_of( arr ) === 'array' ? arr : [arr];
        return arr[ this.random_segment_number( arr.length )];
    }


    //************************************************************* 

    shuffle_array( arr ) {
        arr = Array.isArray( arr ) ? arr : [arr];
        if ( arr.length > 1 )
             arr = arr.sort(function(){ return Math.random() - 0.5});
        return arr;
    }

    /************************************************************* 
     * usage: var l = lw( ); // create a lib instance
     *        var wait = l.async_delay( milli_seonds );
     *        var delayed_task = wait( task ); // task can be an object, a function or any other data type
     * 
     * return values: 
     *      - returns a closure with milli_seconds as variable
     *      - a function: with an object that it will use in the resolve process once finished
     * 
     ************************************************************* 
    */
    async_delay( t ) {
        return function( v ) {
            return new Promise( res => setTimeout(( ) => res( v ), t ));
        }
    }

    //************************************************************* 
    
    is_array( a ) {
        return Boolean( this.type_of( a ) === 'array' );
    }

    //************************************************************* 
    
    assert_is_array( a ) {
        return this.is_array( a ) ? a : [ a ];
    }

    //************************************************************* 

    is_key( key ) {
        const supported_types = ['boolean','object','string','number', 'array'];

        return {
            in: ( container ) => {
                  const t = this.type_of( container );
                  if ( supported_types.includes( t ))  {
                       switch ( this.type_of( container )) {
                            case 'boolean': return container === key;
                            case 'array'  : return container.includes( key );
                            case 'object' : return key in container;
                            case 'string' : return container.includes( key.toString( ));
                            case 'number' : return container.toString( ).includes( key.toString( ));
                       }
                  }else throw new Error('is_key.in function does not support that container type');
            }
        }
    }


    map( o, f ) {
        return Object.fromEntries(Object.entries( o ).map(( [k, v] ) => [k, f( v )]));
    }    
    
}
window.macleen_LwJs = LwJs;