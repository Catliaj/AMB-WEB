# Booking and Reservation System - Complete Implementation Summary

## Overview
This document summarizes the complete booking and reservation workflow system, including all status transitions, payment calculations, and contract generation.

## Status Flow

### Booking Statuses (ENUM: 'Pending', 'Scheduled', 'Cancelled', 'Rejected')

1. **Pending** - Initial status when client books a viewing
   - Visible to both client and agent
   - Agent can: Approve → Scheduled, Reject → Rejected
   - Client can: View details only

2. **Scheduled** - Agent approved the viewing
   - Remains in bookings (it's still a "viewing")
   - Client can: Cancel → Cancelled, Reserve → Moves to Reservations
   - Agent can: View details only

3. **Rejected** - Agent rejected the viewing
   - Client can: View details only (no further actions)
   - Agent can: View details only

4. **Cancelled** - Client cancelled the booking
   - Client can: View details only (no further actions)
   - Agent can: View details only

### Reservation Statuses (ENUM: 'Ongoing', 'Completed', 'Defaulted', 'Cancelled')

1. **Ongoing** - Reservation created from Scheduled booking
   - Client can: Cancel → Cancelled (moves back to bookings), Select Payment, Sign Contract
   - After payment selection: Can Sign Contract
   - After contract signing: Status → Completed

2. **Completed** - Contract signed and PDF generated
   - Final state, no further actions

3. **Cancelled** - Reservation cancelled by client
   - Moves back to bookings with Cancelled status
   - No further actions except view details

## Payment Calculation Formula

### Loan Modes:
- **Pagibig**: Maximum 60 years
- **Banko**: Maximum 30 years  
- **Full Paid**: No calculation (property price)

### Calculation Logic:
```
For Pagibig/Bank loans:
  years = maxYears (60 for Pagibig, 30 for Banko)
  months = years * 12
  monthlyPayment = propertyPrice / months

For Full Payment:
  monthlyPayment = propertyPrice (one-time payment)
```

### Example:
- Property Price: ₱1,000,000
- Client Age: 25
- Mode: Pagibig (60 years max)
- Calculation:
  - years = 60
  - months = 60 * 12 = 720
  - monthlyPayment = 1,000,000 / 720 = ₱1,388.89

## Complete Workflow

### 1. Client Books Viewing
- **Action**: Client clicks "Book Viewing" on property
- **Status**: Created as 'Pending'
- **Visible**: Both client's "My Bookings" and agent's bookings

### 2. Agent Approves/Rejects
- **Approve**: Status → 'Scheduled'
- **Reject**: Status → 'Rejected' (with optional reason in Notes)
- **Result**: Client sees updated status on bookings page

### 3. Client Actions on Scheduled Booking
- **Cancel**: Status → 'Cancelled', remains in bookings
- **Reserve**: Creates entry in `houserreservation` table, booking status → 'Reserved', moves to reservations page

### 4. Reservation - Select Payment
- **Action**: Client clicks "Select Payment"
- **Process**:
  1. Fetches client's birthdate from database
  2. Calculates age
  3. Shows modal with:
     - Property price
     - Client age
     - Radio buttons: Pagibig, Banko, Full Paid
     - Dynamic calculation display
  4. Client selects payment mode
  5. Calculation updates automatically
  6. Client confirms payment
  7. Updates reservation with `Term_Months` and `Monthly_Amortization`

### 5. Reservation - Sign Contract
- **Action**: Client clicks "Sign Contract" (appears after payment selection)
- **Process**:
  1. Opens signature pad modal (using SignaturePad.js library)
  2. Client draws signature on canvas
  3. Client clicks "Sign & Generate Contract"
  4. Signature converted to base64 PNG
  5. Backend:
     - Saves signature as BLOB in `houserreservation.Buyer_Signature`
     - Fetches all user data from database:
       - Client: FirstName, MiddleName, LastName, Email, Phone, Birthdate
       - Agent: FirstName, LastName, Email, Phone
       - Property: Title, Location, Type, Size, Bedrooms, Bathrooms, Price, Description
       - Reservation: Term_Months, Monthly_Amortization, DownPayment
     - Generates PDF contract with all information
     - Saves PDF to `writable/contracts/`
     - Updates reservation status → 'Completed'
  6. Returns PDF URL to client

### 6. Contract PDF Contents
The generated PDF includes:
- **Parties**: Team Leader (Agent) and Client full names, emails, phones
- **Property**: Full address, type, size, bedrooms, bathrooms, price
- **Terms**: Contract period, start date, termination date
- **Payment**: Monthly payment amount, deposit amount
- **Furnishings**: Property description
- **Signature**: Client's digital signature image
- **Date**: Signing date (day, month, year)

## Database Tables

### `booking` Table
- `bookingID` (Primary Key)
- `UserID` (Foreign Key → users)
- `PropertyID` (Foreign Key → property)
- `BookingDate` (DATE)
- `Status` (ENUM: 'Pending', 'Scheduled', 'Cancelled', 'Rejected')
- `Reason` (TEXT)
- `Notes` (TEXT)
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

### `houserreservation` Table
- `reservationID` (Primary Key)
- `bookingID` (Foreign Key → booking)
- `DownPayment` (DECIMAL)
- `Term_Months` (INT)
- `Monthly_Amortization` (DECIMAL)
- `Buyer_Signature` (BLOB)
- `Status` (ENUM: 'Ongoing', 'Completed', 'Defaulted', 'Cancelled')
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

## API Endpoints

### Client Endpoints:
- `POST /bookings/create` - Create new booking
- `GET /bookings/mine` - Get client's bookings
- `GET /bookings/reservations` - Get client's reservations
- `POST /bookings/cancel` - Cancel booking/reservation
- `POST /users/reserve` - Reserve a scheduled booking
- `POST /users/selectPayment` - Select payment method
- `POST /users/signContract` - Sign contract with signature

### Agent Endpoints:
- `GET /users/agentbookings` - Get agent's bookings
- `POST /users/updateBookingStatus` - Approve/Reject booking
- `GET /users/getBooking/{id}` - Get booking details

## Key Features Implemented

1. ✅ Status-based workflow with proper transitions
2. ✅ Age calculation from birthdate
3. ✅ Dynamic payment calculation based on loan mode
4. ✅ Digital signature pad integration
5. ✅ PDF contract generation with all user data from database
6. ✅ Proper database column casing (PascalCase)
7. ✅ Reservation cancellation moves back to bookings
8. ✅ Complete user information fetching for contracts

## Files Modified

### Controllers:
- `app/Controllers/UserController.php` - Client booking/reservation actions
- `app/Controllers/AgentController.php` - Agent booking approval/rejection

### Models:
- `app/Models/BookingModel.php` - Booking database operations
- `app/Models/ReservationModel.php` - Reservation database operations

### Views:
- `app/Views/Pages/client/bookings.php` - Client bookings page
- `app/Views/Pages/client/reservations.php` - Client reservations page
- `app/Views/Pages/agent/bookings.php` - Agent bookings page

### JavaScript:
- `assets/js/bookings.js` - Client-side booking/reservation logic

## Testing Checklist

- [ ] Client can create booking (status: Pending)
- [ ] Agent can see pending bookings
- [ ] Agent can approve booking (status: Scheduled)
- [ ] Agent can reject booking (status: Rejected)
- [ ] Client can see scheduled bookings
- [ ] Client can cancel scheduled booking (status: Cancelled)
- [ ] Client can reserve scheduled booking (moves to reservations)
- [ ] Client can see reservations
- [ ] Client can cancel reservation (moves back to bookings)
- [ ] Client can select payment mode
- [ ] Payment calculation works correctly for all modes
- [ ] Client can sign contract
- [ ] PDF contract generates with all user data
- [ ] Contract PDF includes signature
- [ ] Reservation status updates to Completed after signing

## Notes

- All database columns use PascalCase (Status, UserID, PropertyID, etc.)
- The payment calculation uses the full maximum term for the loan mode
- Signature is saved as BLOB in database and embedded in PDF
- PDF contracts are saved in `writable/contracts/` directory
- All user data is fetched from database for contract generation

