class Timer {

    static TIMER_PORTION  = 0.05;

    constructor( $jquery_dom_timer, $jquery_dom_countdown, delay ) {
      this.timer_instance   = null;
      this.dom_timer        = $jquery_dom_timer;
      this.dom_countdown    = $jquery_dom_countdown;
      this._delay           = delay;
      this.cycle_count_down = delay;
    }

    //-----------------------------

    set delay( v ) {
        if ( isNaN( v ))
             throw new Error('Delay must be a whole number')
        this._delay = v;     
    }

    //-----------------------------

    get delay( ) {
        return this._delay;
    }    

   //-----------------------------

    #evoluate(w) {
        this.dom_timer.css("width", ( w <= 0 ? 0 : w >= 100 ? 100 : w ) + '%');
        this.dom_countdown.text( Number( this.cycle_count_down ).toFixed( 0 ));
    }

    //-----------------------------

    reset() {
        this.cycle_count_down = this._delay;
        this.#evoluate(0);
    }

    //-----------------------------

    stop() {
        clearInterval(this.timer_instance);
        this.reset();
    }    


    #timer_cycle( ...args ){

        let self = this;
        this.reset();
        return new Promise( resolve => {
            let portions = self._delay / Timer.TIMER_PORTION;
            let cycle_slice = 100 / portions;
            let timer_evolution = 0;
            clearInterval(self.timer_instance);
            self.reset();
            self.timer_instance = setInterval(
                function() {
                    timer_evolution   += cycle_slice;
                    self.#evoluate(timer_evolution);
                    self.cycle_count_down -= Timer.TIMER_PORTION;
                    if ( timer_evolution >= 100 ) {
                        clearInterval(self.timer_instance);
                        resolve(...args );
                    }
                }, 
                Timer.TIMER_PORTION * 1000);
        })
    }


    run_timer_cycle( caller, ...args ) {
        this.#timer_cycle(...args ).then( caller )
    }


}