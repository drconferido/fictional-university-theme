import $ from 'jquery';
class Search {
    // 1. describe and create/initiate our object
    constructor() {
            // this.name = "Jane Doe";
            this.addSearchHTML();
            this.resultsDiv = $("#search-overlay__results");
            this.openButton = $('.js-search-trigger');
            this.closeButton = $('.search-overlay__close');
            this.searchOverlay = $('.search-overlay');
            this.searchField= $("#search-term");
            this.events();
            this.isOverlayOpen = false;
            this.isSpinnerVisible = false;
            this.previousValue;
            this.typingTimer;
    }
    // 2. events
// on this.head feels cold, wearsHat
events() {
    this.openButton.on('click', this.openOverlay.bind(this));
    this.closeButton.on('click', this.closeOverlay.bind(this));
    $(document).on('keydown', this.keyPressDispatcher.bind(this));
    this.searchField.on('keyup', this.typingLogic.bind(this));
}

keyPressDispatcher(e) {

if((e.keyCode === 83 || e.keyCode === 70) && !this.isOverlayOpen && !$("input, textarea").is(':focus')){
        this.openOverlay();
    }
if(e.keyCode==27 && this.isOverlayOpen){
    this.closeOverlay();
    }
}
    // 3. methods (function, action...)
// wearsHat() { }
// hairCut() {}
typingLogic() {
  if(this.searchField.val() != this.previousValue){
clearTimeout(this.typingTimer);
        if(this.searchField.val()){
            if(this.isSpinnerVisible == false){
            this.resultsDiv.html('<div class="spinner-loader"></div>');
            this.isSpinnerVisible = true;
            }
        this.typingTimer = setTimeout(this.getResults.bind(this), 760);
        }else{
            this.resultsDiv.html('');
            this.isSpinnerVisible = false;
        }
  }

  this.previousValue = this.searchField.val();
}

getResults() {    
    $.getJSON(universityDataRootUrl.root_url + '/wp-json/ficUniverity/v1/search?term=' + this.searchField.val(), (dataResults) =>{
                this.resultsDiv.html(`
                  <div class="row">
                  <div class="one-third">
                  <h2 class="section-overlay__section-title">General Information</h2>
                  ${dataResults.generalInfo.length ? ' <ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                  ${dataResults.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType === 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
                  ${dataResults.generalInfo.length ? '</ul>' : ''}
                  </div>
                  <div class="one-third">
                  <h2 class="section-overlay__section-title">Programs</h2>
                   ${dataResults.programs.length ? ' <ul class="link-list min-list">' : `<p>No programs match that search. <a href="${universityDataRootUrl.root_url}/programs/">View all programs</a></p>`}
                  ${dataResults.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                  ${dataResults.programs.length ? '</ul>' : ''}


                  <h2 class="section-overlay__section-title">Professors</h2>
                   ${dataResults.professors.length ? ' <ul class="professor-cards">' : '<p>No professors match that search.</p>'}
                  ${dataResults.professors.map(item => `
                       <li class="professor-card__list-item">
                      <a class="professor-card" href="${item.permalink}">
                          <img class="professor-card__image" src="${item.image}">
                          <span class="professor-card__name">${item.title}</span>
                      </a>
                    </li>
                  `).join('')}
                  ${dataResults.professors.length ? '</ul>' : ''}
                  </div>
                  <div class="one-third">
                  <h2 class="section-overlay__section-title">Campuses</h2>
                  ${dataResults.campuses.length ? ' <ul class="link-list min-list">' : `<p>No campuses match that search. <a href="${universityDataRootUrl.root_url}/campuses/">View all campuses</a></p>`}
                  ${dataResults.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                  ${dataResults.campuses.length ? '</ul>' : ''}


                  <h2 class="section-overlay__section-title">Events</h2>
                  ${dataResults.events.length ? '' : `<p>No events match that search. <a href="${universityDataRootUrl.root_url}/events/">View all events</a></p>`}
                  ${dataResults.events.map(item => `
                     <div class="event-summary">
             <a class="event-summary__date t-center" href="${item.permalink}">
              <span class="event-summary__month">${item.month}</span>
                      <span class="event-summary__day">${item.day}</span>
            </a>
             <div class="event-summary__content">
              <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}"> ${item.title} </a></h5>
              <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p>
          </div>  
            </div>
                    `).join('')}
               
                  </div>
                  </div>  
                    `);
                    this.isSpinnerVisible = false;
    });
}

openOverlay() {
    this.searchOverlay.addClass('search-overlay--active');
    $('body').addClass('body-no-scroll');
    this.searchField.val('');
    setTimeout(() => this.searchField.focus(), 301);
    console.log("Our open method just ran!");
    this.isOverlayOpen = true;
    }

closeOverlay() {
    this.searchOverlay.removeClass('search-overlay--active');
    $('body').removeClass('body-no-scroll');
    console.log("Our close method just ran!");
     this.isOverlayOpen = false;
    }

    addSearchHTML() {
        $('body').append(`
            <div class="search-overlay">
  <div class="search-overlay__top">
    <div class="container">
      <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
      <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
         <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
    </div>
  </div>
  <div class="container">
    <div id="search-overlay__results"></div>
  </div>
</div>`); 
    }
}
export default Search