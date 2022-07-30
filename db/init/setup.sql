

/*
    Create a table to store the users.
    Each user has an ID that is assigned to them when the annotator is
    initalised if there is not already one set. This allows a user to change
    which ontology they are annotating with, without losing any existing annotations.
    The lastUsed attribute stores the timestamp that the user used the annotator.
*/
CREATE TABLE user (
    id int,
    lastUsed int,

    PRIMARY KEY (id)
);



/*
    Create table to store the ontology.
    Each ontology has a unique id attribute, the serialised content of the 
    Ontology object and the timestamp that it was last accessed.
*/
CREATE TABLE ontology (
    id int,
    userID int,
    content LONGTEXT,
    lastAccessed int,

    PRIMARY KEY (id),
    FOREIGN KEY (userID) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
);

/*
    Create table to store the resources that are being annotated.
    Each resource has a unique id, the id of the ontology that it is being 
    annotated from, the DOI identifier, the resource name, 
    the author's name, and the date.
*/
CREATE TABLE resource (
    id int,
    ontologyID int, 
    identifier varchar(255),
    name varchar(255),
    author varchar(255),
    date varchar(10),

    savedAt int,

    PRIMARY KEY (id),
    FOREIGN KEY (ontologyID) REFERENCES ontology(id) ON DELETE CASCADE ON UPDATE CASCADE
);


/*
    Create table to store resource id to term URI pairs.
*/
CREATE TABLE term (
    resourceID int,
    termURI varchar(255),

    PRIMARY KEY (resourceID, termURI),
    FOREIGN KEY (resourceID) REFERENCES resource(id) ON DELETE CASCADE ON UPDATE CASCADE
);