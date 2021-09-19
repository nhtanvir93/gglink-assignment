Tables

1. groups
    - id
    - name
    - definition

2. avatars
    - id
    - path
    - content_type
    - size
    - extension

3. users
    - id
    - username
    - email
    - password
    - avatar_id

4. group_user
    - id
    - user_id
    - group_id




Seeders

1. GroupSeeder
    a. id - 1, name - Default
    b. id - 2, name - api_user_add
    c. id - 3, name - api_user_delete
    d. id - 4, name - api_user_detail
    e. id - 5, name - api_user_all
    f. id - 6, name - api_user_profile
    g. id - 7, name - api_user_update



Config

1. custom_settings
    a. is_registration_available - to star/stop registration
    b. api_key
    c. token_validity (minutes) - token last validity time in minutes
    d. upload_paths.avatar - directory path where avatar will be uploaded
    e. default_group_id - 1 (Default)
    f. default_avatar_path - default_avatar.png (Stored in public directory and will be shown if no avatar given)
    g. permissions - an array containing exception groups against requested uri



Middleware

1. VerifyApiKey (verify.api-key) - Check 'X-API-KEY' validity
2. AuthRequestedUser (verify.user) - Check 'X-TOKEN' validity (If valid token is found, that user will be set to auth user manually)
3. CheckingPermission (check.permission) - Check group permission of a logged in user



Exception

1. File - app/Exceptions/Handler.php
    a. ValidationException - If validation errors occured while adding, updating, deleting user, system will give response containing message 'ValidationError' with    status 422
    b. AuthorizationException - It is mainly used to handle AuthorizationException of registration request when registration is off



File Storage
    - Avatar will be stored in 'storage/app/public'
    - To access these avatar from web, symbolic link needs to be created which will help to access directory 'storage/app/public' from 'public/storage'
    - command to create symbolic link - php artisan storage:link



Business Logic Architeture
    - Controllers resides in API directory which will do main business logic funtionalities
    - Repositories in app/Repositories will do sql related works
    - Form request will filter the invalid store/update/delete requests
    - Model will help to make sql related works easy
    - No data is deleted physically, only soft deletetion is enabled in the application



Tasks 

1. Add User 
    - if no group_ids is given, group_id 1 (Default) will be set for that user
    - if no avatar is given, no avatar data is stored in 'avatars' table
    - if avatar is given, it will be uploaded first and then corresponding avatar data will stored in avatars table
    - data will be stored in users table using mass assignment criteria
    - mutator is used to hash the password

2.  Update User 
    - if no group_ids is given, group_id 1 (Default) will be set for that user but before storing new group_ids old group_ids will be deleted
    - if no avatar is given, no avatar data is stored in 'avatars' table
    - if avatar is given, it will be uploaded first and then corresponding avatar data will stored in avatars table
    - data will be stored in users table using mass assignment criteria
    - mutator is used to hash the password

3. User Detail
    - id will be fetched from query 'Id' param, if not found 0 will be set by default
    - if no avatar is found for the request user, public url of default_avatar.png will be set
    - if no model found then application will catch NoModelFoundException and return response accordingly 

4. User Delete
    - valid id (not deleted user id) will be checked through form request
    - user data will be soft deleted

5. User Report
    1. Filter data will be following
        a. ?Filter='email=example@gmail.com;username=test_user;group=1:2' (multiple group searching)
        b. ?Filter='email=example@gmail.com;username=test_user;group=1'
    2. Field
        a. If multiple field is given, it should match the exact column of users table
            - id, username, email

6. Login
    a. first search user data using username
    b. if user is found, password will be check using Hash facade
    c. if username and password are matched, then token will be created using Str::uuid
    d. Then token and token_last_validity_timestamp will be updated for that user

7. Logout
    a. If no logged in user is found according to 'X-TOKEN', logout will not be applicable
    b. If logged in user is found, then token and token_last_validity_timestamp will be set to null for the requested logged in user