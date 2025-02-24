window.addEventListener("app:mounted", (function() {

  const config = { 
    sortField: { 
        field: "text", 
        direction: "asc" 
    }
  };

  const config2 = {
    plugins: ["remove_button"]
  };
  
  if($('#tomOne').length) {
    const tomOne = document.querySelector("#tomOne");
    tomOne._tom = new Tom(tomOne, config);
  }

  if($('#tomTwo').length) {
    const tomTwo = document.querySelector("#tomTwo");
    tomTwo._tom = new Tom(tomTwo, config);
  }

  if($('#tomThree').length) {
    const tomThree = document.querySelector("#tomThree");
    tomThree._tom = new Tom(tomThree, config);
  }

  if($('#tomFour').length) {
    const tomFour = document.querySelector("#tomFour");
    tomFour._tom = new Tom(tomFour, config);
  }

  if($('#tomSelectClear').length) {
    const tomSelectClear = document.querySelector("#tomSelectClear");
    tomSelectClear._tom = new Tom(tomSelectClear, config2);
  }
}), {
  once: !0
});