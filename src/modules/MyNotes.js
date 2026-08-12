import $ from 'jquery'

class MyNotes{
    constructor(){
       this.events();
    }

    events() {
        $(".delete-note").on("click", this.deleteNote);
    }
    deleteNote() {
        alert("you clicked the delete button");
    }

    //methods will go here
}

export default MyNotes;