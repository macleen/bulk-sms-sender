class Streamer {
    
    constructor(settings) {

        
        this.cycle_tick       = 0;
        this.settings         = settings;
        this.generator        = settings.generator();
        this.delayTime        = settings.delayTime * 1000;        // Convert to milliseconds
        
        this.pause$           = new rxjs.BehaviorSubject(false);  // Control streams
        this.resume$          = new rxjs.Subject();
        this.stop$            = new rxjs.Subject();

        this.subscription     = new rxjs.Subscription();
        this.isStopped        = false;
        this.isStreamComplete = false;

        this.setupButtons();

    }

    setupButtons() {
        this.settings.stopButton   ? this.settings.stopButton.addEventListener('click' , ( )=> this.stop( false )) : void(0);
        this.settings.pauseButton  ? this.settings.pauseButton.addEventListener('click', ( )=> this.pause())       : void(0)
        this.settings.resumeButton ? this.settings.resumeButton.addEventListener('click',( )=> this.resume())      : void(0) 
    }


    #createMessageStream() {

        return new rxjs.Observable(observer => {

            const sendNext = () => {
                if (this.isStopped) {
                    observer.complete();
                    return;
                }

                if (this.pause$.getValue()) {
                    console.log("⏸️ Paused. Waiting for resume...");
                    this.resume$.pipe(rxjs.take(1)).subscribe(() => {
                        console.log("▶️ Resumed!");
                        sendNext();
                    });
                    return;
                }

                const { value: packet, done } = this.generator.next();
                if (done) {
                    observer.complete();
                    this.settings.onStreamEnd();
                    this.isStreamComplete = true;
                    return;
                }

                this.settings.sendMessage(packet)
                    .then(response => {
                        this.settings.onMessageSent(response);
                        this.cycle_tick = 0; 

                        // After message is sent, wait for the delay before continuing
                        if (!this.isStopped && !this.isStreamComplete) {
                            if (this.pause$.getValue()) {
                                this.resume$.pipe(rxjs.take(1)).subscribe(() => sendNext());
                            } else {
                                rxjs.timer(this.delayTime).subscribe(() => sendNext());
                            }
                        }
                    })
                    .catch(error => {
                        console.error("❌ Message sending error:", error);
                        observer.error(error);
                    });
            };

            sendNext();
        });
    }

    start_ticker( ){
        this.tick_handle = setInterval(() => this.settings.onTick( this.cycle_tick++ ), 1400 ); // ~1/2sec more to accomodate for server delays
        return this;
    }

    stop_ticker( ){
        clearInterval( this.tick_handle );
        return this;
    }
    start() {

        this.isStopped        = false;
        this.isStreamComplete = false;

        this.settings.onStart( );

        const messageStream$ = this.#createMessageStream().pipe(
            rxjs.takeUntil(this.stop$)
        );
        
        this.start_ticker( ).subscription.add(
            messageStream$.subscribe({
                next: (message) => console.log("📩 Processed message:", message),
                error: (err) => {
                    console.error("❌ Error in stream:", err);
                    this.settings.onError(err);
                    this.stop( false );
                },
                complete: () => {
                    console.log("✅ Streaming finished.");
                    this.settings.onStreamEnd();
                    this.stop( true );
                }
            })
        );
    }

    stop( success ) {
        this.isStopped = true;
        this.stop$.next();
        this.cleanup();

        this.settings.onEnd( success );
    }

    pause() {
        this.pause$.next(true);
        this.settings.onPause();
        this.stop_ticker( );
    }

    resume() {
        this.pause$.next(false);
        this.resume$.next();
        this.settings.onResume();
        this.start_ticker( );
    }

    cleanup() {
        this.stop_ticker( );
        this.subscription.unsubscribe();
        this.subscription = new rxjs.Subscription();
    }
}