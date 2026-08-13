import $ from 'jquery'

class MyNotes{
    constructor(){
       this.events();
    }

    events() {
        $(".delete-note").on("click", this.deleteNote);
        $(".edit-note").on("click", this.editNote.bind(this));
        $(".update-note").on("click", this.updateNote.bind(this));
    }
    editNote(e) {
          var thisNote = $(e.target).parents("li");
          if(thisNote.data("state") == "editable"){
            this.makeNoteReadOnly(thisNote);
          }else{
            this.makeNoteEditable(thisNote);
          }
    }
    
updateNote(e) {
        var thisNote = $(e.target).parents("li");
        var noteTitle = thisNote.find(".note-title-field").val();
        var noteBody = thisNote.find(".note-body-field").val();

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityDataRootUrl.nonce);
            },
            url: universityDataRootUrl.root_url + "/wp-json/wp/v2/note/"+ thisNote.data("id"),
            type: "POST",
            data: {
                title: noteTitle,
                content: noteBody
            },
            success:(response) =>{
                this.makeNoteReadOnly(thisNote);
                console.log("Congrats, your note was updated!");
                console.log(response);
            },
            error: (response) =>{
                console.log("Sorry, but your note was not updated.");
                console.log(response);
            }
        });
    }

    makeNoteEditable(thisNote) {

         thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
          thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
          thisNote.find(".update-note").addClass("update-note--visible");
          thisNote.data("state", "editable");
    }

    makeNoteReadOnly(thisNote) {
          thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
          thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
          thisNote.find(".update-note").removeClass("update-note--visible");
            thisNote.data("state", "cancel");
    }

    deleteNote(e) {
        var thisNote = $(e.target).parents("li");
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityDataRootUrl.nonce);
                },
                url: universityDataRootUrl.root_url + "/wp-json/wp/v2/note/"+ thisNote.data("id"),
                type: "DELETE",
                success:(response) =>{
                    thisNote.slideUp();
                    console.log("Congrats, your note was deleted!");
                    console.log(response);
                },
                error: (response) =>{
                    console.log("Sorry, but your note was not deleted.");
                    console.log(response);
                }
            });
    }

    //methods will go here
}

export default MyNotes;