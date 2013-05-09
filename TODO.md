- How can we make IDs update? Is that something for the UnitOfWork to do
  as it goes?  Would make rolling back more complex.

- can we remove Manager from the relation proper, and leave it only for the
  relation builder? would inject the actual type object dependencies, not the
  manager as a service locator. The *builder* should do all the checking. It's
  OK if the builder uses the manager, as long as the relations themselves get
  only the objects. Does the builder even need the related-field name?
  Info keys:

    - native
    - native_type
    - native_field
    - foreign
    - foreign_type
    - foreign_field
    - through
    - through_type
    - through_native_field
    - through_foreign_field
