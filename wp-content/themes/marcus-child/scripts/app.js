(function() {

  const Controller = {

    getVars: function() {
      this.message = document.querySelector('.offer-header')
      console.log(this.message)
    },

    init: function() {
      document.addEventListener("DOMContentLoaded", function(event) {
        Controller.getVars()
      });
    }
  }

Controller.init()
}())
