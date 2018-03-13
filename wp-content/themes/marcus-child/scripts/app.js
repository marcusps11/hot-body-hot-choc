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
       //create two variables for holding the date for 30 back from now using    substract
       var back30Days=moment().subtract(1, 'days').format('YYYY-MM-DD H:mm:ss');
       let timeUntilMidnight = Math.abs(Math.floor(moment().diff(moment().hour(24).minute(00).second(0), 'seconds')));
       let wholeHoursLeft = timeUntilMidnight / 3600;
       let arrayOfHours = wholeHoursLeft.toString().split('.');
       let hoursToMultiply = parseFloat(arrayOfHours.shift())
       let convertHoursToSecondsAndGetNewTotal = 3600 * hoursToMultiply;
       let newTotal = (24 - wholeHoursLeft) * convertHoursToSecondsAndGetNewTotal;
       let totalHoursMinusNewTotalInSeconds = 86400 - newTotal;
       let howManyHoursLeft = (totalHoursMinusNewTotalInSeconds / 3600);
       let wholeHours = howManyHoursLeft.toString().split('.').shift();
       let secondsToMinutes = parseFloat(3600 * wholeHours);
       let finalTotal = totalHoursMinusNewTotalInSeconds - secondsToMinutes;
       let remainingTime = (totalHoursMinusNewTotalInSeconds - secondsToMinutes) / 60;
      let fullMins = parseFloat(remainingTime.toString().split('.').shift());
       let secondsLeft = (finalTotal -  60 * fullMins )
        document.getElementById("hours").innerHTML=wholeHours + ' Hours';
        document.getElementById("minutes").innerHTML=fullMins+ ' Minutes';
        document.getElementById("seconds").innerHTML=secondsLeft + ' Seconds';

      },1000)
    }
  }

Controller.init()
}())
