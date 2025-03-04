class Gauge {
    constructor(delay) {
        if (isNaN(delay) || delay < 1) {
            throw new Error('Delay must be a whole non-zero number');
        }

        this.totalSegments = delay;
        this.singleSegmentWidth = 100 / this.totalSegments;
        this.domGauge     = jQuery('#gauge-meter');
        this.domCountdown = jQuery('#delay-timer-countdown');

        this.reset();
    }

    draw(segment) {
        segment = Math.max(0, Math.min(segment, this.totalSegments));
        this.domGauge.css("width", (segment * this.singleSegmentWidth) + '%');
        this.domCountdown.text(Number(segment).toFixed(0));
    }

    reset() {
        this.draw(0);
    }

    stop() {
        this.reset();
    }

    updateUI(segment) {
        this.draw(segment);
    }
}