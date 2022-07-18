
/*
    Create table to store the ontology.
    Each ontology has a unique id attribute,
    the serialised content of the Ontology object
    and the timestamp that it was last accessed.
*/
CREATE TABLE ontology (
    id int,
    content text,
    accessed int,

    PRIMARY KEY (id)
);

/*
    Create table to store the resources that are being annotated.
    Each resource has a unique id, the id of the ontology 
    that it is being annotated from, the DOI identifier, 
    the resource name, the author's name, and the date.
*/
CREATE TABLE resource (
    id int,
    ontologyID int, 
    identifier varchar(255),
    name varchar(255),
    author varchar(255),
    date int,

    PRIMARY KEY (id),
    FOREIGN KEY (ontologyID) REFERENCES ontology(id)
);


/*
    Create table to store resource id to term URI pairs.
*/
CREATE TABLE term (
    resourceID int,
    termURI varchar(255),

    PRIMARY KEY (resourceID, termURI),
    FOREIGN KEY (resourceID) REFERENCES resource(id)
);