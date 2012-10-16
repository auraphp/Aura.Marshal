GenericType
-----------

- Add methods ...

    - deleteRecord() capability; note that this only marks it for deletion,
      and does not actually do anyting in a data store. Should this also
      cascade through subordinate relationships?
    
    - removeRecord() to remove from the IdentityMap without marking for
      deletion.
    
    - getDeletedRecords() to get a collection of records that were deleted
      using deleteRecord()


GenericCollection
-----------------

- Add methods ...

    - deleteRecord() to remove from the collection and mark for deletion.
      Should this cascade through subordinate relationships?

    - removeRecord() to remove from the collection without marking for
      deletion (and without removing from the IdentityMap).
