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

       //create two variables for holding the date for 30 back from now using    substract
       var back30Days=moment().subtract(1, 'days').format('YYYY-MM-DD H:mm:ss');
       var countDownSeconds= Math.floor(moment().diff(back30Days, 'seconds'));
       let resetCLock = (Math.floor(moment().diff(moment().hour(23).minute(59).second(0), 'seconds')));
       let timeUntilMidday = Math.abs(Math.floor(moment().diff(moment().hour(24).minute(00).second(0), 'seconds')));
       let wholeHoursLeft = timeUntilMidday / 3600;
       let arrayOfHours = wholeHoursLeft.toString().split('.');
       let hoursToMultiply = parseFloat(arrayOfHours.shift())
       let convertHoursToSecondsAndGetNewTotal = 3600 * hoursToMultiply;
       let newTotal = (24 - wholeHoursLeft) * convertHoursToSecondsAndGetNewTotal;
       let totalHoursMinusNewTotalInSeconds = 86400 - newTotal;
       let howManyHoursLeft = (totalHoursMinusNewTotalInSeconds / 3600);
       console.log( `there are ${howManyHoursLeft} full hours left `)
       let wholeHours = howManyHoursLeft.toString().split('.').shift();
       let secondsToMinutes = parseFloat(3600 * wholeHours);
       let finalTotal = totalHoursMinusNewTotalInSeconds - secondsToMinutes;
       let remainingTime = (totalHoursMinusNewTotalInSeconds - secondsToMinutes) / 60;
       console.log(`there are ${remainingTime} minutes left`)
      let fullMins = parseFloat(remainingTime.toString().split('.').shift());
       let secondsLeft = (finalTotal -  60 * fullMins )
      console.log(`There re ${wholeHours} hours, ${fullMins} minutes, ${secondsLeft} secoonds left till midnight`)

      //  let minutesLeft =
      // 23111 seconds
      // 6 hours

      // 23111 - 3 * 3600 = 1511

      // 1511 / 60 = 25.183 minutes

      // 18 seconds

      // let hoursLeft = timeUntilMidday / 3600;
      // let arrayOfHoursLeft = timeUntilMidday.toString().split('.');
      // let Hours = parseFloat(y.shift());
      // let leftOverTimeMinusHours = timeUntilMidday - hoursToMinus * 3600;
      // let minutesLeft =  leftOverTimeMinusHours / 60;

      // window.setInterval(() => {
      //   timeUntilMidday++
      //   if(timeUntilMidday === 0) {
      //     timeUntilMidday = resetCLock;
      //   }
      //   let hoursLeft = timeUntilMidday / 3600;
      //   let arrayOfHoursLeft = timeUntilMidday.toString().split('.');
      //   let Hours = parseFloat(arrayOfHoursLeft.shift());
      //   let leftOverTimeMinusHours = timeUntilMidday - Hours * 3600;
      //   let minutesLeft =  leftOverTimeMinusHours / 60;
      //   console.log(arrayOfHoursLeft,  Hours, leftOverTimeMinusHours, minutesLeft)
      // },1000)

      //  = 0 hours 2 minutes 10 seconds
      //  set an interval every second we want to munuts from this number
      //  if this the countdown equals 0 we want to reset the time till mignight

      //   Hours =  60 = 3600 seconds in an hour



         //variables holding days, hours , minutes and seconds
         var Days, Minutes,Hours,Seconds;
         // Set Interval function for performing all calculations and decrementing the countDownSeconds
         setInterval(function(){

         // Updating Days
          Days =pad(Math.floor(countDownSeconds / 86400),2);
         // Updating Hours
        Hours = pad(Math.floor((countDownSeconds - (Days * 86400)) / 3600),2);
         // Updating Minutes
        Minutes =pad(Math.floor((countDownSeconds - (Days * 86400) - (Hours * 3600)) / 60),2);
       // Updating Seconds
          Seconds = pad(Math.floor((countDownSeconds - (Days * 86400) - (Hours* 3600) - (Minutes * 60))), 2);

         // Updation our HTML view
        document.getElementById("hours").innerHTML=Hours + ' Hours';
        document.getElementById("minutes").innerHTML=Minutes+ ' Minutes';
        document.getElementById("seconds").innerHTML=Seconds + ' Seconds';

            // Decrement the countDownSeconds
        countDownSeconds--;

             // If we reach zero , hour chrono should rest to 30 days back again, as you told
       if(countDownSeconds=== 0){
           countDownSeconds= Math.floor(moment().diff(back30Days, 'seconds'));
       }

        },1000);
         // Function for padding the seconds i.e limit it only to 2 digits
           function pad(num, size) {
               var s = num + "";
               while (s.length < size) s = "0" + s;
               return s;
         }
    }
  }

Controller.init()
}())
