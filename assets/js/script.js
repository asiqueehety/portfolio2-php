function toggleMenu() {
  document.querySelector(".sidebar").classList.toggle("active");
}

function toggleSettings() {
  const el = document.querySelector(".admin-link"); // change to your element
  if (el.style.transform === "translateX(0px)" || el.style.transform === "translateX(0)") {
    el.style.transform = "translateX(-150%)"; // hide
  } else {
    el.style.transform = "translateX(0)"; // show
  }
}





