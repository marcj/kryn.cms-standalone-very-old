/**
 *
 * @event done(ka.SaveProgress saveProgress)
 * @event progress(ka.SaveProgress saveProgress)
 * @event cancel(ka.SaveProgress saveProgress)
 *
 * @event allDone(value, this)
 * @event allProgress(Number progress, this)
 *
 * @type {Class}
 */
ka.SaveProgressManager = new Class({
    Extends: ka.SaveProgress,

    saveProgress: [],
    allProgressDone: false,

    /**
     *
     * @param {Object} options
     * @param {*}      context
     */
    initialize: function(options, context) {
        this.parent(options, context);

        this.addEvent('done', this.updateDone.bind(this));
        this.addEvent('cancel', this.updateDone.bind(this));
        this.addEvent('error', this.updateDone.bind(this));
        this.addEvent('progress', this.updateProgress.bind(this));
    },

    updateProgress: function() {
        var progressValue = 0;
        var progressMax = 0;

        Array.each(this.saveProgress, function(progress) {
            progressMax += progress.getProgressRange();
            progressValue += progress.isFinished() ? progress.getProgressRange() : progress.getProgress();
        }.bind(this));

        progressValue = progressValue * 100 / progressMax;
        if (this.currentProgress !== progressValue) {
            this.allProgress(progressValue);
        }
    },

    updateDone: function() {
        this.updateProgress();

        var allDone = true;
        Array.each(this.saveProgress, function(progress) {
            if (!progress.isDone() && !progress.isCanceled() && !progress.isErrored()) {
                allDone = false;
            }
        }.bind(this));

        if (this.allProgressDone !== allDone) {
            this.allProgressDone = allDone;
            this.allDone();
        }
    },

    allProgress: function(progress) {
        this.currentProgress = progress;
        this.fireEvent('allProgress', [this.currentProgress, this]);
    },

    /**
     * Fires the 'allDone' event with the given value.
     * @param {*} value
     */
    allDone: function(value) {
        this.state = true;
        this.value = value;
        this.fireEvent('allDone', [this.value, this]);
    },

    /**
     * @param {ka.SaveProgress} saveProgress
     */
    done: function(saveProgress) {
        this.fireEvent('done', saveProgress);
    },

    /**
     * @returns {Boolean}
     */
    isAllDone: function() {
        return this.allProgressDone;
    },

    /**
     * @param {ka.SaveProgress} saveProgress
     */
    progress: function(saveProgress) {
        this.fireEvent('progress', saveProgress);
    },

    /**
     * @param {Object} options
     * @param {*} context
     *
     * @returns {ka.SaveProgress}
     */
    newSaveProgress: function(options, context) {
        var progress = new ka.SaveProgress(options, context);

        progress.addEvent('done', function() {
            this.fireEvent('done', progress);
        }.bind(this));

        progress.addEvent('cancel', function() {
            this.fireEvent('cancel', progress);
        }.bind(this));

        progress.addEvent('progress', function() {
            this.fireEvent('progress', progress);
        }.bind(this));

        this.saveProgress.push(progress);
        return progress
    },

    /**
     * @param {ka.SaveProgress} saveProgress
     */
    addSaveProgress: function(saveProgress) {
        this.saveProgress.push(saveProgress);
    },

    getAllSaveProgress: function() {
        return this.saveProgress;
    }
});