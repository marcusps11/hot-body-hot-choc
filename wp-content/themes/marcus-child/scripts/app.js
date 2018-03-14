const moment = require('moment');

(function() {

  const Controller = {

    getVars: function() {
      this.message = document.querySelector('.offer-header')
      this.getTime()
    },

    init: function() {
      document.addEventListener("DOMContentLoaded", function(event) {
        Controller.getVars()
      });
    },

    getTime: function() {

      window.setInterval(() => {
      let timeUntilMidnight = Math.abs(Math.floor(moment().diff(moment().hour(24).minute(00).second(0), 'seconds')));
       //create two variables for holding the date for 30 back from now using    substract
      //  let timeUntilMidnight = Math.abs(Math.floor(moment().diff(moment().hour(24).minute(00).second(0), 'seconds')));
       let wholeHoursLeft = timeUntilMidnight / 3600;
       let arrayOfHours = wholeHoursLeft.toString().split('.');
      //  This is how many whole hours there are until midnight
       let hoursToMultiply = parseFloat(arrayOfHours.shift())
        // This is how many seconds are left in the day
       let convertHoursToSecondsAndGetNewTotal = 3600 * hoursToMultiply;
       let howManySecondsHavePassedToday = 86400 - convertHoursToSecondsAndGetNewTotal;

       let totalTimeLeft = timeUntilMidnight - convertHoursToSecondsAndGetNewTotal;
       let minsLeft = (totalTimeLeft / 60);
       let parsedMins = parseFloat(minsLeft.toString().split('.').shift());
       let final = totalTimeLeft - (parsedMins * 60);

        document.getElementById("hours").innerHTML=hoursToMultiply + ' Hours';
        document.getElementById("minutes").innerHTML=parsedMins+ ' Minutes';
        document.getElementById("seconds").innerHTML=final + ' Seconds';

      },1000)
    }
  }

Controller.init()
}())
