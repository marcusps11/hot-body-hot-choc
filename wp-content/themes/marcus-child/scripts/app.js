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
