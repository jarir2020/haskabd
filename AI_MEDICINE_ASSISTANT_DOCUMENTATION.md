# AI Medicine Suggestion and Doctor Finder Documentation

## 1. Project Overview

This documentation describes the website's AI-powered medicine suggestion and customer guidance feature in a client-friendly, non-technical way. The feature is designed for an e-commerce website that sells medicine, healthcare products, wellness items, diagnostic products, and related health supplies.

The AI assistant helps customers find relevant products from the store, understand basic product availability, ask shop-related questions, and get guidance toward nearby doctors when the customer's location is known or willingly shared.

The feature should be positioned as a helpful shopping and health-support assistant, not as a replacement for a doctor, pharmacist, or emergency service.

## 2. Main Purpose

Customers often visit a health e-commerce website with questions such as:

- Which medicine or health product may match my need?
- Is this product available?
- What is the price?
- Which category should I browse?
- Do I need to speak with a doctor?
- Is there a doctor near me?

The AI assistant gives quick, friendly, product-aware support so customers can make better shopping decisions and know when professional medical help is needed.

Use:

- Helps customers find relevant medicines or health products faster.
- Reduces repeated support questions.
- Guides customers to safer next steps when symptoms may need a doctor.
- Makes the store feel modern, helpful, and customer-focused.
- Supports doctor discovery when location is known.

## 3. Important Safety Positioning

Because the feature deals with medicine and health questions, the AI should follow a clear safety position:

- The AI may suggest relevant products available in the shop.
- The AI may explain general product information shown on the website.
- The AI may recommend consulting a doctor or pharmacist.
- The AI may suggest nearby doctors when the user shares location or area.
- The AI should not claim to diagnose a disease.
- The AI should not replace a doctor's prescription.
- The AI should not give unsafe dosage instructions.
- The AI should not encourage prescription medicine use without a valid prescription.
- The AI should guide emergency symptoms to urgent medical care.

Recommended message:

"I can help you find relevant products and nearby doctors, but I cannot diagnose illness or replace professional medical advice. Please consult a qualified doctor or pharmacist before taking medicine, especially for serious symptoms, children, pregnancy, chronic disease, allergies, or prescription medicines."

## 4. Existing AI Chat Feature

The current code already supports an AI chat assistant on the website. The assistant can be enabled or disabled from the admin panel and can answer customer questions using shop information.

Current capability:

- AI chat can be shown on the frontend website.
- Admin can choose the AI provider and model.
- Admin can set the AI response length.
- Admin can add custom instructions for the assistant.
- AI can access shop information such as store name, categories, contact details, and product count.
- AI can search relevant products from the catalog based on the customer's message.
- AI can mention product name, category, price, and stock status.
- AI keeps short conversation history during the chat session.
- Customer can clear the chat history.

Use:

- Makes the assistant aware of the store's real products.
- Helps customers receive product suggestions instead of generic answers.
- Lets the business control the AI's tone and boundaries.
- Gives admins flexibility to update instructions without changing the website code.

## 5. Medicine Suggestion Feature

The AI can suggest medicines or health products based on what the customer asks, but the suggestions should be handled as product guidance, not medical diagnosis.

Example customer questions:

- "Do you have medicine for fever?"
- "What can I buy for cough?"
- "Do you sell pain relief medicine?"
- "What products are available for acidity?"
- "Do you have diabetes testing products?"
- "What should I buy for a first aid box?"

Use:

- Helps customers find products available in the store.
- Makes medicine and health product shopping easier.
- Reduces time spent searching manually.
- Helps customers discover related products such as thermometers, test kits, strips, masks, sanitizers, supplements, or first aid items.

Safe answer style:

- Suggest relevant product categories or available products.
- Mention price and stock if available.
- Ask the customer to read product instructions.
- Recommend speaking to a doctor or pharmacist before using medicine.
- Avoid firm statements such as "You have this disease" or "Take this medicine."

## 6. Product-Aware AI Suggestions

The AI can search the product catalog and return relevant products from the store. It can prioritize matching products by name or category.

Use:

- Keeps suggestions connected to items the business actually sells.
- Avoids recommending unavailable products.
- Helps customers move from question to purchase quickly.
- Supports medicines, supplements, diagnostic devices, home test kits, and wellness products.

Example AI response style:

"We have a few products related to fever care. Please check the product details and consult a doctor or pharmacist before using any medicine, especially for children, pregnancy, allergies, or ongoing illness."

## 7. Nearby Doctor Suggestion Feature

The AI can suggest nearby doctors when the user's location is known. Location may come from browser permission, saved customer address, selected area, or manual entry such as city, district, or neighborhood.

Use:

- Helps customers move from self-care to professional care when needed.
- Makes the platform more helpful than a normal medicine store.
- Builds trust by guiding customers to qualified healthcare support.
- Supports customers who are unsure which doctor to contact.

Important rule:

- The AI should only use location after the user gives permission or provides an area manually.
- If location is not known, the AI should ask the user to share location or type their area.
- Location should be used only to find nearby doctors, clinics, hospitals, or pharmacies.

## 8. Doctor Suggestion Flow

Suggested user journey:

1. Customer asks a medicine or symptom-related question.
2. AI gives safe product guidance.
3. AI identifies whether professional care may be needed.
4. AI asks for location permission or area name if location is missing.
5. Customer shares location or enters city/area.
6. AI suggests nearby doctors or clinics.
7. Customer sees doctor name, specialty, address, phone number, chamber time, distance, and appointment option if available.
8. Customer can choose to call, view map, or request appointment.

Use:

- Creates a smooth bridge between e-commerce and healthcare support.
- Helps customers avoid guessing when symptoms need professional attention.
- Makes the store a trusted health-support platform.

## 9. Doctor Recommendation Content

Doctor suggestions should be clear and practical.

Recommended information:

- Doctor name.
- Specialty.
- Clinic or chamber name.
- Address.
- Distance from user.
- Phone number.
- Consultation hours.
- Appointment link or call button.
- Consultation fee, if available.
- Online consultation availability, if available.
- Verification or partner badge, if applicable.

Use:

- Helps users compare nearby doctors quickly.
- Makes the recommendation useful beyond just a name.
- Supports both in-person and online consultation.

## 10. Location-Based Doctor Matching

The system can match doctors based on:

- User location.
- User selected area.
- Symptom or product category.
- Doctor specialty.
- Availability.
- Distance.
- Partner status.
- User language preference.
- Consultation type, such as in-person or online.

Use:

- Gives more relevant doctor suggestions.
- Helps users find the correct type of specialist.
- Makes location sharing valuable to the customer.

Example matching:

- Fever, cold, cough: general physician or medicine specialist.
- Skin rash: dermatologist.
- Child symptoms: pediatrician.
- Pregnancy concern: gynecologist.
- Diabetes products or glucose readings: endocrinologist or diabetologist.
- Heart/blood pressure concern: cardiologist or medicine specialist.
- Eye drops or eye symptoms: eye specialist.

## 11. Emergency Guidance

The AI should recognize serious symptoms and avoid only suggesting products.

Emergency examples:

- Chest pain.
- Severe breathing difficulty.
- Stroke-like symptoms.
- Severe allergic reaction.
- Loss of consciousness.
- Severe bleeding.
- Severe dehydration.
- Very high fever with danger signs.
- Severe pain.
- Pregnancy emergency.
- Child emergency symptoms.

Use:

- Helps customers understand when they should not delay care.
- Protects users from relying on product suggestions in urgent cases.
- Supports a responsible healthcare brand image.

Recommended AI response:

"This may need urgent medical attention. Please contact emergency services or visit the nearest hospital immediately. If you share your location, I can help show nearby hospitals or doctors."

## 12. Customer Panel Features

### AI Chat Access

Customers can open the AI assistant from the website or user account.

Use:

- Gives quick support while shopping.
- Helps users ask natural questions instead of searching manually.
- Makes the experience friendly for non-technical customers.

### Medicine/Product Suggestion History

The user panel may show previous AI product suggestions or recent chat history.

Use:

- Helps customers revisit suggested products.
- Supports repeat purchases.
- Makes the assistant more useful for returning customers.

### Saved Location or Area

Customers can save their preferred city, district, or delivery area.

Use:

- Allows faster doctor suggestions later.
- Improves delivery and location-based recommendations.
- Avoids asking for location every time.

### Nearby Doctors Section

The user panel can include a "Nearby Doctors" area based on the user's saved location.

Use:

- Makes doctor discovery easy outside the chat.
- Helps customers find medical help when needed.
- Adds healthcare value to the platform.

### Suggested Products List

Customers can see AI-suggested products and add them to cart.

Use:

- Converts AI guidance into sales.
- Makes product selection easier.
- Helps customers remember what the AI recommended.

### Safety Notices

The user panel should show health and medicine safety notices where needed.

Use:

- Encourages responsible medicine use.
- Reduces misunderstanding of AI suggestions.
- Builds trust.

## 13. Admin Panel Features

### AI Chat Settings

Admins can manage the AI assistant from the admin panel.

Current settings:

- Provider.
- Model.
- API key.
- Custom instruction.
- Response length.
- Enable or disable frontend chat.

Use:

- Gives the business control over AI behavior.
- Lets admins update shop rules, delivery policy, medicine warning text, and support tone.
- Allows the assistant to be turned off if needed.

### Medicine Suggestion Rules

Admins should be able to define what the AI can and cannot suggest.

Use:

- Keeps medicine suggestions safe.
- Prevents promotion of restricted products.
- Helps the business control product categories.

Suggested rules:

- Allow suggestions for general wellness and approved over-the-counter products.
- Require doctor/pharmacist advice for prescription medicine.
- Block dosage instructions unless they come from approved product labeling.
- Show allergy and pregnancy warnings.
- Encourage doctor visit for serious symptoms.
- Never claim a guaranteed cure.

### Custom AI Instructions

Admins can write instructions that guide how the AI behaves.

Use:

- Helps the AI match the business style.
- Adds medicine-specific safety boundaries.
- Allows the client to define product categories, delivery policy, return policy, and doctor-referral messages.

Recommended custom instruction:

"You are a friendly medicine and healthcare product assistant. Suggest only products available in our shop. Do not diagnose disease, prescribe medicine, or give dosage advice. For prescription medicine, serious symptoms, pregnancy, children, allergies, chronic illness, or uncertainty, recommend consulting a doctor or pharmacist. If the user's location is known, suggest nearby doctors or clinics when professional care may be needed."

### Doctor Directory Management

Admins can manage doctor information used by the nearby-doctor feature.

Use:

- Allows the website to recommend real doctors.
- Keeps doctor details updated.
- Supports partner doctors or clinics.

Doctor fields:

- Doctor name.
- Photo.
- Specialty.
- Qualifications.
- Clinic/chamber name.
- Address.
- City/area.
- Map location.
- Phone number.
- Appointment link.
- Consultation time.
- Online consultation availability.
- Fee, if shown.
- Status active/inactive.
- Verified or partner badge.

### Specialty Management

Admins can create and manage doctor specialties.

Use:

- Helps match symptoms or product categories to the right doctor type.
- Keeps doctor discovery organized.
- Supports filtering by specialty.

Example specialties:

- General Physician.
- Medicine Specialist.
- Pediatrician.
- Gynecologist.
- Dermatologist.
- Cardiologist.
- Endocrinologist.
- ENT Specialist.
- Eye Specialist.
- Dentist.
- Nutritionist.

### Location and Area Management

Admins can manage service areas, districts, cities, or zones.

Use:

- Helps match users with nearby doctors.
- Supports delivery area and doctor area mapping.
- Keeps location recommendations more accurate.

### Symptom-to-Specialty Mapping

Admins can map common user concerns to doctor specialties.

Use:

- Helps the AI suggest relevant doctors.
- Makes doctor recommendations more useful.
- Reduces random or poor matches.

Example mapping:

- Fever: General Physician.
- Child fever: Pediatrician.
- Skin itching: Dermatologist.
- High sugar: Endocrinologist.
- Chest discomfort: Cardiologist or emergency care.
- Pregnancy question: Gynecologist.

### Emergency Keyword Management

Admins can define terms that trigger emergency guidance.

Use:

- Helps the AI avoid casual product suggestions for serious conditions.
- Improves customer safety.
- Keeps the assistant aligned with business responsibility.

### Product Category Controls

Admins can choose which product categories are allowed for AI suggestions.

Use:

- Allows AI to suggest safe categories first.
- Prevents restricted medicines from being casually recommended.
- Lets the business control product exposure.

### AI Conversation Monitoring

Admins may review anonymized or consent-based AI chat trends.

Use:

- Helps improve product catalog and FAQ content.
- Shows what customers commonly ask.
- Helps identify missing products or confusing categories.

Privacy note:

- Personal health-related messages should be handled carefully and access should be limited.

### AI Enable/Disable Control

Admins can turn the AI assistant on or off.

Use:

- Useful during maintenance.
- Allows quick response if AI answers need review.
- Gives the client operational control.

## 14. Customer Experience Examples

### Example 1: Simple Medicine Product Search

Customer asks:

"Do you have medicine for acidity?"

AI response style:

"We have products related to acidity and digestion support. Please review the product details before buying. If your symptoms are severe, repeated, or you have other health conditions, please consult a doctor or pharmacist."

Use:

- Suggests relevant products.
- Keeps the answer safe.
- Encourages professional advice when needed.

### Example 2: Doctor Suggestion with Location

Customer asks:

"I have a fever for three days. Any medicine?"

AI response style:

"A fever lasting three days should be discussed with a doctor. I can show fever-care products available in the shop, but please consult a doctor. Since your location is set to Dhanmondi, here are nearby general physicians and clinics."

Use:

- Provides product support.
- Adds medical caution.
- Uses location to guide the user to real care.

### Example 3: Location Not Known

Customer asks:

"Can you suggest a doctor near me?"

AI response style:

"Sure. Please allow location access or type your area, such as city or neighborhood, and I can suggest nearby doctors or clinics."

Use:

- Makes location sharing optional.
- Keeps the experience user-controlled.

### Example 4: Emergency Situation

Customer asks:

"I have chest pain and breathing problem. What medicine should I take?"

AI response style:

"This may require urgent medical attention. Please call emergency services or go to the nearest hospital immediately. If you share your location, I can help show nearby hospitals or emergency care options."

Use:

- Avoids unsafe medicine suggestion.
- Directs user to urgent care.

## 15. E-Commerce Benefits

The AI assistant supports the normal e-commerce business model while adding a smarter support layer.

Use:

- Helps customers find products faster.
- Increases product discovery.
- Improves conversion from chat to cart.
- Reduces manual customer support workload.
- Encourages repeat purchases.
- Builds a healthcare-focused brand identity.

## 16. Healthcare Brand Benefits

Adding nearby doctor suggestions makes the website feel more responsible and helpful.

Use:

- Builds customer trust.
- Shows that the platform values safety, not only sales.
- Creates partnership opportunities with doctors and clinics.
- Makes the business stronger than a normal online medicine shop.

## 17. Privacy Considerations

The feature may involve sensitive customer questions and location data, so privacy should be clear.

Recommended approach:

- Ask permission before using location.
- Let users enter location manually if they prefer.
- Avoid storing exact location unless needed.
- Limit admin access to health-related chat details.
- Show a privacy notice near location sharing.
- Allow users to clear chat history.

Use:

- Builds customer confidence.
- Reduces privacy concerns.
- Makes the doctor finder feel safe to use.

## 18. Suggested Future Features

### Add to Cart from AI Chat

Customers can add suggested products directly from the chat.

Use:

- Makes shopping faster.
- Turns AI suggestions into purchases.

### Appointment Booking

Customers can book a doctor appointment from the doctor suggestion list.

Use:

- Adds another service channel.
- Supports doctor partnerships.

### Online Consultation

Customers can request online consultation with listed doctors.

Use:

- Helps users who cannot visit physically.
- Makes the platform more useful for remote support.

### Prescription Upload

Customers can upload a prescription before buying restricted medicine.

Use:

- Supports safer medicine sales.
- Helps verify prescription-required products.

### Medicine Reminder

Customers can set reminders for purchased medicines.

Use:

- Adds customer value after purchase.
- Encourages app-like engagement.

### Refill Reminder

Customers can receive reminders for repeat medicine or health product purchase.

Use:

- Supports recurring sales.
- Helps customers avoid running out of important products.

### Doctor Review and Rating

Customers can rate listed doctors after appointments.

Use:

- Builds trust in doctor listings.
- Helps other users choose doctors.

### Pharmacy or Clinic Locator

The location feature can also show nearby pharmacies, clinics, hospitals, or diagnostic centers.

Use:

- Expands the platform's health-support value.
- Helps users get urgent or local support.

## 19. Suggested Client Handover Requirements

Before launching this feature, the client should prepare:

- Final medicine and health product categories.
- Product names, prices, descriptions, stock, and warnings.
- List of products allowed for AI suggestion.
- List of restricted or prescription-only products.
- Standard safety disclaimer text.
- Emergency keyword list.
- Doctor directory with location and specialty.
- Partner doctor or clinic information.
- Location privacy policy.
- AI custom instruction text.
- Admin rules for medicine suggestion.
- Process for reviewing AI conversations and improving responses.

## 20. Final Positioning Statement

This feature should be presented as an AI-powered medicine and healthcare product assistant with doctor discovery support. The strongest positioning is:

"The assistant helps customers find relevant health products, understand store availability, and connect with nearby doctors when professional care may be needed."

It should not be marketed as an AI doctor. It should not promise diagnosis, cure, or prescription decisions. With the right safety messaging, product controls, and nearby doctor feature, it can become a powerful trust-building layer for a healthcare e-commerce website.
