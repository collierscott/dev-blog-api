Feature: Manage blog posts
  @createSchema
  Scenario: Create a blog post
    Given I am authenticated as "admin"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
      "title": "Hello a title",
      "content": "The content is suppose to be at least 20 characters",
      "slug": "a-new-slug"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {
    "@context": "/api/contexts/BlogPost",
    "@id": "/api/blog_posts/101",
    "@type": "BlogPost",
    "id": 101,
    "title": "Hello a title",
    "publishedAt": "2018-10-31T14:54:05+00:00",
    "content": "The content is suppose to be at least 20 characters",
    "slug": "a-new-slug",
    "author": "/api/users/11",
    "comments": [],
    "images": []
}
    """